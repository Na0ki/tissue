import React, { useEffect, useState } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Link, Route, Routes, useNavigate, useParams, useSearchParams } from 'react-router-dom';
import { Button, Form, Modal, ModalProps, OverlayTrigger, Spinner, Tooltip } from 'react-bootstrap';
import { LinkCard } from '../components/LinkCard';
import { MyProfileContext, useMyProfile } from '../context';
import { useFetchMyProfile, useFetchCollections, useFetchCollectionItems, useFetchCollection } from '../api';
import { Pagination } from '../components/Pagination';
import { showToast } from '../tissue';
import { fetchDeleteJson, fetchPostJson, fetchPutJson, ResponseError } from '../fetch';
import classNames from 'classnames';
import { MetadataPreview } from '../components/MetadataPreview';
import { TagInput } from '../components/TagInput';
import { FieldError } from '../components/FieldError';

type CollectionFormValues = {
    title: string;
    is_private: boolean;
};

type CollectionFormErrors = {
    [Property in keyof CollectionEditFormValues]+?: string[];
};

class CollectionFormValidationError extends Error {
    errors: CollectionFormErrors;

    constructor(errors: CollectionFormErrors, ...rest: any) {
        super(...rest);
        if (Error.captureStackTrace) {
            Error.captureStackTrace(this, CollectionFormValidationError);
        }

        this.name = 'CollectionFormValidationError';
        this.errors = errors;
        Object.setPrototypeOf(this, new.target.prototype);
    }
}

interface CollectionEditModalProps extends ModalProps {
    mode: 'create' | 'edit';
    initialValues: CollectionFormValues;
    onSubmit: (values: CollectionFormValues) => Promise<void>;
}

const CollectionEditModal: React.FC<CollectionEditModalProps> = ({
    mode,
    initialValues,
    onSubmit,
    show,
    onHide,
    ...rest
}) => {
    const [values, setValues] = useState(initialValues);
    const [errors, setErrors] = useState<CollectionFormErrors>({});
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (show) {
            setValues(initialValues);
            setErrors({});
        }
    }, [show]);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setSubmitting(true);
        try {
            await onSubmit(values);
        } catch (e) {
            if (e instanceof CollectionFormValidationError) {
                setErrors(e.errors);
                return;
            }
            throw e;
        } finally {
            setSubmitting(false);
        }
    };

    const handleHide = () => {
        if (!submitting && onHide) {
            setValues(initialValues);
            setErrors({});
            onHide();
        }
    };

    return (
        <Modal show={show} onHide={handleHide} {...rest}>
            <form onSubmit={handleSubmit}>
                <Modal.Header closeButton>
                    <Modal.Title as="h5">コレクションの{mode === 'create' ? '作成' : '設定'}</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <label htmlFor="title">
                                <span className="oi oi-folder" /> タイトル
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                className={classNames({ 'form-control': true, 'is-invalid': errors?.title })}
                                required
                                value={values.title}
                                onChange={(e) => setValues((values) => ({ ...values, title: e.target.value }))}
                            />
                            <FieldError name="title" label="タイトル" errors={errors?.title} />
                        </div>
                    </div>
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <p className="mb-1">
                                <span className="oi oi-eye" /> 公開設定
                            </p>
                            <Form.Check
                                custom
                                inline
                                type="radio"
                                id="collectionItemVisibilityPublic"
                                label="公開"
                                checked={!values.is_private}
                                onChange={() => setValues((values) => ({ ...values, is_private: false }))}
                            />
                            <Form.Check
                                custom
                                inline
                                type="radio"
                                id="collectionItemVisibilityPrivate"
                                label="非公開"
                                className="mt-2"
                                checked={values.is_private}
                                onChange={() => setValues((values) => ({ ...values, is_private: true }))}
                            />
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" disabled={submitting} onClick={handleHide}>
                        キャンセル
                    </Button>
                    {submitting ? (
                        <Button type="submit" variant="primary" disabled={submitting}>
                            <Spinner
                                className="mr-1"
                                as="span"
                                animation="border"
                                size="sm"
                                role="status"
                                aria-hidden="true"
                            />
                            {mode === 'create' ? '作成' : '更新'}中…
                        </Button>
                    ) : (
                        <Button type="submit" variant="primary">
                            {mode === 'create' ? '作成' : '更新'}
                        </Button>
                    )}
                </Modal.Footer>
            </form>
        </Modal>
    );
};

type SidebarItemProps = {
    collection: Tissue.Collection;
};

const SidebarItem: React.FC<SidebarItemProps> = ({ collection }) => {
    const { id } = useParams();

    if (collection.id == id) {
        return (
            <li className="list-group-item d-flex justify-content-between align-items-center active">
                <div style={{ wordBreak: 'break-all' }}>
                    <span className="oi oi-folder mr-1" />
                    {collection.title}
                </div>
            </li>
        );
    } else {
        return (
            <Link
                to={`/user/${collection.user_name}/collections/${collection.id}`}
                className="list-group-item d-flex justify-content-between align-items-center text-dark"
            >
                <div style={{ wordBreak: 'break-all' }}>
                    <span className="oi oi-folder text-secondary mr-1" />
                    {collection.title}
                </div>
            </Link>
        );
    }
};

type SidebarProps = {
    collections?: Tissue.Collection[];
    reloadCollections: () => void;
};

const Sidebar: React.FC<SidebarProps> = ({ collections, reloadCollections }) => {
    const me = useMyProfile();
    const { username } = useParams();
    const navigate = useNavigate();
    const [showCreateModal, setShowCreateModal] = useState(false);

    const handleSubmit = async (values: CollectionFormValues) => {
        try {
            const response = await fetchPostJson('/api/collections', values);
            if (response.status === 201) {
                const createdItem = await response.json();
                showToast('作成しました', { color: 'success', delay: 5000 });
                reloadCollections();
                setShowCreateModal(false);
                navigate(`/user/${createdItem.user_name}/collections/${createdItem.id}`);
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    const errors: CollectionFormErrors = {};
                    for (const violation of data.error.violations) {
                        const field = violation.field as keyof CollectionFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    throw new CollectionFormValidationError(errors);
                }
            }
            showToast('エラーが発生しました', { color: 'danger', delay: 5000 });
        }
    };

    if (!collections) {
        return null;
    }

    return (
        <div className="card mb-4">
            <div className="card-header d-flex justify-content-between align-items-center">
                <span>コレクション</span>
                {username === me?.name && (
                    <Button
                        variant="link"
                        className="text-secondary"
                        size="sm"
                        title="追加"
                        onClick={() => setShowCreateModal(true)}
                    >
                        <span className="oi oi-plus" />
                    </Button>
                )}
            </div>
            <div className="list-group list-group-flush">
                {collections.map((collection) => (
                    <SidebarItem key={collection.id} collection={collection} />
                ))}
            </div>
            <CollectionEditModal
                mode="create"
                initialValues={{ title: '', is_private: true }}
                onSubmit={handleSubmit}
                show={showCreateModal}
                onHide={() => setShowCreateModal(false)}
            />
        </div>
    );
};

interface ItemEditModalProps extends ModalProps {
    item: Tissue.CollectionItem;
    onUpdate: (item: Tissue.CollectionItem) => void;
}

type ItemEditFormValues = {
    tags: string[];
    note: string;
};

type ItemEditFormErrors = {
    [Property in keyof ItemEditFormValues]+?: string[];
};

const ItemEditModal: React.FC<ItemEditModalProps> = ({ item, onUpdate, show, onHide, ...rest }) => {
    const [values, setValues] = useState<ItemEditFormValues>({
        note: item.note,
        tags: item.tags,
    });
    const [errors, setErrors] = useState<ItemEditFormErrors>({});
    const [submitting, setSubmitting] = useState<boolean>(false);

    useEffect(() => {
        if (show) {
            setValues({
                note: item.note,
                tags: item.tags,
            });
            setErrors({});
        }
    }, [show]);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setSubmitting(true);
        try {
            const response = await fetchPutJson(`/api/collections/${item.collection_id}/items/${item.id}`, {
                ...values,
                flash: true,
            });
            if (response.status === 200) {
                const updatedItem = await response.json();
                showToast('更新しました', { color: 'success', delay: 5000 });
                onUpdate(updatedItem);
                onHide?.();
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    const errors: ItemEditFormErrors = {};
                    for (const violation of data.error.violations) {
                        const field = violation.field as keyof ItemEditFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    setErrors(errors);
                    return;
                }
            }
            showToast('エラーが発生しました', { color: 'danger', delay: 5000 });
        } finally {
            setSubmitting(false);
        }
    };

    const handleHide = () => {
        if (!submitting && onHide) {
            setValues({ note: item.note, tags: item.tags });
            setErrors({});
            onHide();
        }
    };

    return (
        <Modal show={show} onHide={handleHide} {...rest}>
            <form onSubmit={handleSubmit}>
                <Modal.Header closeButton>
                    <Modal.Title as="h5">編集</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <label htmlFor="link">
                                <span className="oi oi-link-intact" /> オカズリンク
                            </label>
                            <input
                                type="text"
                                id="link"
                                name="link"
                                className="form-control"
                                disabled
                                placeholder="http://..."
                                value={item.link}
                            />
                        </div>
                    </div>
                    <MetadataPreview
                        link={item.link}
                        tags={values.tags}
                        onClickTag={(v) => setValues(({ tags, ...rest }) => ({ ...rest, tags: tags.concat(v) }))}
                    />
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <label htmlFor="tagInput">
                                <span className="oi oi-tags" /> タグ
                            </label>
                            <TagInput
                                id="tagInput"
                                name="tags"
                                values={values.tags}
                                isInvalid={!!errors?.tags}
                                onChange={(v) => setValues((values) => ({ ...values, tags: v }))}
                            />
                            <small className="form-text text-muted">
                                Tab, Enter, 半角スペースのいずれかで入力確定します。
                            </small>
                            <FieldError name="tags" label="タグ" errors={errors?.tags} />
                        </div>
                    </div>
                    <div className="form-row">
                        <div className="form-group col-sm-12">
                            <label htmlFor="note">
                                <span className="oi oi-comment-square" /> ノート
                            </label>
                            <textarea
                                id="note"
                                name="note"
                                className={classNames({ 'form-control': true, 'is-invalid': errors?.note })}
                                rows={4}
                                value={values.note}
                                onChange={(e) => setValues((values) => ({ ...values, note: e.target.value }))}
                            />
                            <small className="form-text text-muted">最大 500 文字</small>
                            <FieldError name="note" label="ノート" errors={errors?.note} />
                        </div>
                    </div>
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" disabled={submitting} onClick={handleHide}>
                        キャンセル
                    </Button>
                    {submitting ? (
                        <Button type="submit" variant="primary" disabled={submitting}>
                            <Spinner
                                className="mr-1"
                                as="span"
                                animation="border"
                                size="sm"
                                role="status"
                                aria-hidden="true"
                            />
                            更新中…
                        </Button>
                    ) : (
                        <Button type="submit" variant="primary">
                            更新
                        </Button>
                    )}
                </Modal.Footer>
            </form>
        </Modal>
    );
};

type CollectionItemProps = {
    item: Tissue.CollectionItem;
    onUpdate: (item: Tissue.CollectionItem) => void;
};

const CollectionItem: React.FC<CollectionItemProps> = ({ item, onUpdate }) => {
    const me = useMyProfile();
    const { username } = useParams();
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [deleted, setDeleted] = useState(false);

    const handleClickDelete = async () => {
        setDeleting(true);
        try {
            const response = await fetchDeleteJson(`/api/collections/${item.collection_id}/items/${item.id}`);
            if (response.ok) {
                setShowDeleteModal(false);
                setDeleted(true);
                showToast('削除しました', { color: 'success', delay: 5000 });
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            showToast('削除中にエラーが発生しました', { color: 'danger', delay: 5000 });
        } finally {
            setDeleting(false);
        }
    };

    if (deleted) {
        return null;
    }

    return (
        <li className="list-group-item border-bottom-only pt-3 pb-3 text-break">
            <div className="row mx-0">
                <LinkCard link={item.link} />
                <p className="d-flex align-items-baseline mb-2 col-12 px-0">
                    <span className="oi oi-link-intact mr-1" />
                    <a className="overflow-hidden" href={item.link} target="_blank" rel="noopener noreferrer">
                        {item.link}
                    </a>
                </p>
            </div>
            {item.tags.length !== 0 && (
                <p className="tis-checkin-tags mb-2">
                    {item.tags.map((tag: string) => (
                        <a
                            key={tag}
                            className="badge badge-secondary"
                            href={`/search/checkin?q=${encodeURIComponent(tag)}`}
                        >
                            <span className="oi oi-tag" /> {tag}
                        </a>
                    ))}
                </p>
            )}
            {item.note != '' && <p className="mb-2 text-break" dangerouslySetInnerHTML={{ __html: item.note }} />}
            <div className="ejaculation-actions">
                <OverlayTrigger
                    placement="bottom"
                    overlay={<Tooltip id={`checkin_${item.id}`}>このオカズでチェックイン</Tooltip>}
                >
                    <button
                        type="button"
                        className="btn btn-link text-secondary"
                        onClick={() => (location.href = item.checkin_url)}
                    >
                        <span className="oi oi-check" />
                    </button>
                </OverlayTrigger>
                <span className="dropdown">
                    <OverlayTrigger
                        placement="bottom"
                        overlay={<Tooltip id={`add_collection_${item.id}`}>コレクションに追加</Tooltip>}
                    >
                        <button type="button" className="btn btn-link text-secondary" data-toggle="dropdown">
                            <span className="oi oi-plus" />
                        </button>
                    </OverlayTrigger>
                    <div className="dropdown-menu">
                        <h6 className="dropdown-header">コレクションに追加</h6>
                        <button type="button" className="dropdown-item use-later-button" data-link={item.link}>
                            あとで抜く
                        </button>
                    </div>
                </span>
                {username === me?.name && (
                    <>
                        <OverlayTrigger placement="bottom" overlay={<Tooltip id={`edit_${item.id}`}>編集</Tooltip>}>
                            <button
                                type="button"
                                className="btn btn-link text-secondary"
                                onClick={() => setShowEditModal(true)}
                            >
                                <span className="oi oi-pencil" />
                            </button>
                        </OverlayTrigger>
                        <OverlayTrigger placement="bottom" overlay={<Tooltip id={`delete_${item.id}`}>削除</Tooltip>}>
                            <button
                                type="button"
                                className="btn btn-link text-secondary"
                                onClick={() => setShowDeleteModal(true)}
                            >
                                <span className="oi oi-trash" />
                            </button>
                        </OverlayTrigger>
                    </>
                )}
            </div>
            <ItemEditModal
                item={item}
                show={showEditModal}
                onHide={() => setShowEditModal(false)}
                onUpdate={onUpdate}
            />
            <Modal show={showDeleteModal} onHide={() => !deleting && setShowDeleteModal(false)}>
                <Modal.Header closeButton>
                    <Modal.Title as="h5">削除確認</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <a className="link-label" href={item.link} target="_blank" rel="noopener noreferrer">
                        {item.link}
                    </a>{' '}
                    をコレクションから削除してもよろしいですか？
                </Modal.Body>
                <Modal.Footer>
                    <Button variant="secondary" disabled={deleting} onClick={() => setShowDeleteModal(false)}>
                        キャンセル
                    </Button>
                    <Button variant="danger" disabled={deleting} onClick={handleClickDelete}>
                        削除
                    </Button>
                </Modal.Footer>
            </Modal>
        </li>
    );
};

type CollectionHeaderProps = {
    collection: Tissue.Collection;
    onUpdate: (collection: Tissue.Collection) => void;
};

type CollectionEditFormValues = {
    title: string;
    is_private: boolean;
};

type CollectionEditFormErrors = {
    [Property in keyof CollectionEditFormValues]+?: string[];
};

const CollectionHeader: React.FC<CollectionHeaderProps> = ({ collection, onUpdate }) => {
    const me = useMyProfile();
    const [showEditModal, setShowEditModal] = useState(false);
    const [values, setValues] = useState<CollectionEditFormValues>({
        title: collection.title,
        is_private: collection.is_private,
    });
    const [errors, setErrors] = useState<CollectionEditFormErrors>({});
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (showEditModal) {
            setValues({
                title: collection.title,
                is_private: collection.is_private,
            });
            setErrors({});
        }
    }, [showEditModal]);

    const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        setSubmitting(true);
        try {
            const response = await fetchPutJson(`/api/collections/${collection.id}`, values);
            if (response.status === 200) {
                const updatedItem = await response.json();
                showToast('更新しました', { color: 'success', delay: 5000 });
                onUpdate(updatedItem);
                setShowEditModal(false);
                return;
            }
            throw new ResponseError(response);
        } catch (e) {
            console.error(e);
            if (e instanceof ResponseError && e.response.status == 422) {
                const data = await e.response.json();
                if (data.error?.violations) {
                    const errors: CollectionEditFormErrors = {};
                    for (const violation of data.error.violations) {
                        const field = violation.field as keyof CollectionEditFormErrors;
                        (errors[field] || (errors[field] = [])).push(violation.message);
                    }
                    setErrors(errors);
                    return;
                }
            }
            showToast('エラーが発生しました', { color: 'danger', delay: 5000 });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <div className="border-bottom">
            <div className="d-flex justify-content-between align-items-center">
                <h4 className="mb-1">{collection.title}</h4>
                {me?.id === collection.user_id && (
                    <Button variant="secondary" size="sm" onClick={() => setShowEditModal(true)}>
                        設定
                    </Button>
                )}
            </div>
            <p className="mb-3">
                {collection.is_private ? (
                    <small className="text-secondary">
                        <span className="oi oi-lock-locked mr-1" />
                        非公開コレクション
                    </small>
                ) : (
                    <small className="text-secondary">
                        <span className="oi oi-lock-unlocked mr-1" />
                        公開コレクション
                    </small>
                )}
            </p>
            <Modal show={showEditModal} onHide={() => !submitting && setShowEditModal(false)}>
                <form onSubmit={handleSubmit}>
                    <Modal.Header closeButton>
                        <Modal.Title as="h5">コレクションの設定</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <div className="form-row">
                            <div className="form-group col-sm-12">
                                <label htmlFor="title">
                                    <span className="oi oi-folder" /> タイトル
                                </label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    className={classNames({ 'form-control': true, 'is-invalid': errors?.title })}
                                    required
                                    value={values.title}
                                    onChange={(e) => setValues((values) => ({ ...values, title: e.target.value }))}
                                />
                                <FieldError name="title" label="タイトル" errors={errors?.title} />
                            </div>
                        </div>
                        <div className="form-row">
                            <div className="form-group col-sm-12">
                                <p className="mb-1">
                                    <span className="oi oi-eye" /> 公開設定
                                </p>
                                <Form.Check
                                    custom
                                    inline
                                    type="radio"
                                    id="collectionItemVisibilityPublic"
                                    label="公開"
                                    checked={!values.is_private}
                                    onChange={() => setValues((values) => ({ ...values, is_private: false }))}
                                />
                                <Form.Check
                                    custom
                                    inline
                                    type="radio"
                                    id="collectionItemVisibilityPrivate"
                                    label="非公開"
                                    className="mt-2"
                                    checked={values.is_private}
                                    onChange={() => setValues((values) => ({ ...values, is_private: true }))}
                                />
                            </div>
                        </div>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button
                            variant="secondary"
                            disabled={submitting}
                            onClick={() => !submitting && setShowEditModal(false)}
                        >
                            キャンセル
                        </Button>
                        {submitting ? (
                            <Button type="submit" variant="primary" disabled={submitting}>
                                <Spinner
                                    className="mr-1"
                                    as="span"
                                    animation="border"
                                    size="sm"
                                    role="status"
                                    aria-hidden="true"
                                />
                                更新中…
                            </Button>
                        ) : (
                            <Button type="submit" variant="primary">
                                更新
                            </Button>
                        )}
                    </Modal.Footer>
                </form>
            </Modal>
        </div>
    );
};

type CollectionProps = {
    onUpdate: (collection: Tissue.Collection) => void;
};

const Collection: React.FC<CollectionProps> = ({ onUpdate }) => {
    const { id } = useParams();
    const [searchParams] = useSearchParams();
    const fetchCollection = useFetchCollection({ id: id as string });
    const fetchCollectionItems = useFetchCollectionItems({
        id: id as string,
        page: searchParams.get('page'),
    });

    const handleCollectionUpdate = (collection: Tissue.Collection) => {
        fetchCollection.setData(collection);
        onUpdate(collection);
    };

    const handleItemUpdate = (item: Tissue.CollectionItem) => {
        fetchCollectionItems.setData((items) => items?.map((i) => (i.id === item.id ? item : i)));
    };

    return (
        <>
            {fetchCollection.data && (
                <CollectionHeader collection={fetchCollection.data} onUpdate={handleCollectionUpdate} />
            )}
            {fetchCollectionItems.data && (
                <ul className="list-group">
                    {fetchCollectionItems.loading ? null : fetchCollectionItems.data.length === 0 ? (
                        <li className="list-group-item border-bottom-only">
                            <p>このコレクションにはまだオカズが登録されていません。</p>
                        </li>
                    ) : (
                        fetchCollectionItems.data.map((item) => (
                            <CollectionItem key={item.id} item={item} onUpdate={handleItemUpdate} />
                        ))
                    )}
                </ul>
            )}
            {fetchCollectionItems.totalCount && (
                <Pagination
                    className="mt-4 justify-content-center"
                    perPage={20}
                    totalCount={fetchCollectionItems.totalCount}
                />
            )}
        </>
    );
};

const Page: React.FC = () => {
    const { username } = useParams();
    const fetchMyProfile = useFetchMyProfile();
    const fetchCollections = useFetchCollections({ username: username as string });

    const handleUpdate = (collection: Tissue.Collection) => {
        fetchCollections.setData((col) => col?.map((c) => (c.id === collection.id ? collection : c)));
    };

    return (
        <MyProfileContext.Provider value={fetchMyProfile.data}>
            <div className="container">
                <div className="row">
                    <div className="col-lg-4">
                        <Sidebar collections={fetchCollections.data} reloadCollections={fetchCollections.reload} />
                    </div>
                    <div className="col-lg-8">
                        {fetchCollections.error?.response?.status === 403 && (
                            <p className="mt-4">
                                <span className="oi oi-lock-locked" /> このユーザはチェックイン履歴を公開していません。
                            </p>
                        )}
                        {fetchCollections.data?.length === 0 && <p className="mt-4">コレクションがありません。</p>}
                        <Collection onUpdate={handleUpdate} />
                    </div>
                </div>
            </div>
        </MyProfileContext.Provider>
    );
};

ReactDOM.render(
    <BrowserRouter>
        <Routes>
            <Route path="/user/:username/collections/:id" element={<Page />} />
        </Routes>
    </BrowserRouter>,
    document.getElementById('app')
);
