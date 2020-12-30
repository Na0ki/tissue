<?php

namespace Tests\Unit\MetadataResolver;

use App\MetadataResolver\DLsiteResolver;
use Tests\TestCase;

class DLsiteResolverTest extends TestCase
{
    use CreateMockedResolver;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->shouldUseMock()) {
            sleep(1);
        }
    }

    public function testHome()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testHome.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/home/work/=/product_id/RJ221761.html');
        $this->assertEquals('ひつじ、数えてあげるっ', $metadata->title);
        $this->assertEquals('サークル名: Butterfly Dream' . PHP_EOL . '眠れないあなたに彼女が羊を数えてくれる音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ222000/RJ221761_img_main.jpg', $metadata->image);
        $this->assertEquals(['ほのぼの', 'バイノーラル/ダミヘ', '恋人同士', '日常/生活', '癒し'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/home/work/=/product_id/RJ221761.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testSoft()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testSoft.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/soft/work/=/product_id/VJ011276.html');
        $this->assertEquals('ことのはアムリラート', $metadata->title);
        $this->assertEquals('ブランド名: SukeraSparo' . PHP_EOL . '異世界へと迷い込んだ凜に救いの手を差し伸べるルカ――。これは、ふたりが手探りの意思疎通（ことのは）で織りなす、もどかしくも純粋な……女の子同士の物語。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/professional/VJ012000/VJ011276_img_main.jpg', $metadata->image);
        $this->assertEquals(['少女', '日常/生活', '百合', '純愛'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/soft/work/=/product_id/VJ011276.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testComic()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testComic.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/comic/work/=/product_id/BJ138581.html');
        $this->assertEquals('快楽ヒストリエ', $metadata->title);
        $this->assertEquals('著者: 火鳥' . PHP_EOL . '天地創造と原初の人類を描いた「創世編」をはじめ、英雄たちの偉業を大真面目に考証した正真正銘の学術コミック全15編。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/books/BJ139000/BJ138581_img_main.jpg', $metadata->image);
        $this->assertEquals(['おっぱい', 'おやじ', 'ギャグ', 'コメディ', 'ショタ', 'セーラー服', 'ロリ', '女王様/お姫様', '妹', '戦士', '歴史/時代物', '爺', '王子様/王子系', '着物/和服', '褐色/日焼け', '青年コミック'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/comic/work/=/product_id/BJ138581.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testManiax()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testManiax.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/maniax/work/=/product_id/RJ205445.html');
        $this->assertEquals('催眠術で新婚人妻マナカさんとエッチしよう', $metadata->title);
        $this->assertEquals('サークル名: デルタブレード' . PHP_EOL . '催眠術で新婚人妻マナカさんの愛する夫にすり替わって子作りラブラブエッチをするCG集です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ206000/RJ205445_img_main.jpg', $metadata->image);
        $this->assertEquals(['中出し', '人妻', '催眠', '口内射精', '妊娠/孕ませ', '断面図',], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/maniax/work/=/product_id/RJ205445.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testPro()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testPro.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/pro/work/=/product_id/VJ008455.html');
        $this->assertEquals('euphoria （HDリマスター） Best Price版', $metadata->title);
        $this->assertEquals('ブランド名: CLOCK UP' . PHP_EOL . 'インモラルハードコアADV「euphoria」が高解像度（1024×768）版、「euphoria HDリマスター」となって登場！', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/professional/VJ009000/VJ008455_img_main.jpg', $metadata->image);
        $this->assertEquals(['アヘ顔', 'スカトロ', 'マニアック/変態', '女教師', '幼なじみ', '強制/無理矢理', '拘束', '拷問', '狂気', '血液/流血', '退廃/背徳/インモラル'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/pro/work/=/product_id/VJ008455.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testBooks()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testBooks.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/books/work/=/product_id/BJ191317.html');
        $this->assertEquals('永遠娘 vol.6', $metadata->title);
        $this->assertEquals('著者: あまがえる / 玉之けだま / びんせん / 甘露アメ / 源五郎 / すみやお / 宇宙烏賊 / 毒茸人 / あやね / ガロウド / ハードボイルドよし子 / 夜歌 / 黒青郎君' . PHP_EOL . '君の命はどんな味なのだろうな?', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/books/BJ192000/BJ191317_img_main.jpg', $metadata->image);
        $this->assertEquals(['ぶっかけ', 'アヘ顔', 'ストッキング', 'セーラー服', 'ツンデレ', 'ファンタジー', 'メイド', 'ロリ', '中出し', '人外娘/モンスター娘', '口内射精', '妖怪', '近親相姦'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/books/work/=/product_id/BJ191317.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testGirls()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testGirls.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/girls/work/=/product_id/RJ217995.html');
        $this->assertEquals('体イク教師', $metadata->title);
        $this->assertEquals('サークル名: Dusk' . PHP_EOL . '思い込みの激しい体育教師に執着されるお話', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ218000/RJ217995_img_main.jpg', $metadata->image);
        $this->assertEquals(['マニアック/変態', 'レイプ', '中出し', '強制/無理矢理', '教師', '陵辱'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/girls/work/=/product_id/RJ217995.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testGirlsPro()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testGirlsPro.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/girls-pro/work/=/product_id/BJ170641.html');
        $this->assertEquals('×××レクチャー', $metadata->title);
        $this->assertEquals('著者: 江口尋' . PHP_EOL . '【あらすじ】昔、告白してくれた地味な同級生・瀬尾は超人気セクシー男優になっていて!?…', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/books/BJ171000/BJ170641_img_main.jpg', $metadata->image);
        $this->assertEquals(['ティーンズラブ'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/girls-pro/work/=/product_id/BJ170641.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testBL()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testBL.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/bl/work/=/product_id/RJ244977.html');
        $this->assertEquals('秘密に堕つ', $metadata->title);
        $this->assertEquals('サークル名: ナゲットぶん投げ屋さん' . PHP_EOL . 'とある村に越してきた新婚夫婦。村の集会所で行われた歓迎会で犯される花婿。村の男達に犯され続けた花婿にある変化が…?', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ245000/RJ244977_img_main.jpg', $metadata->image);
        $this->assertEquals(['モブ姦', 'レイプ', '中出し', '強制/無理矢理', '既婚者'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/bl/work/=/product_id/RJ244977.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testEng()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testEng.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/eng/work/=/product_id/RE228866.html');
        $this->assertEquals('With Your First Girlfriend, at a Ghostly Night [Ear Cleaning] [Sleep Sharing]', $metadata->title);
        $this->assertEquals('Circle: Triangle!' . PHP_EOL . 'You go with a girl of your first love and enjoy going to haunted places and her massage, ear cleaning, sleep sharing etc. (CV: Yui Asami)', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ229000/RJ228866_img_main.jpg', $metadata->image);
        $this->assertEquals(['ASMR', 'Binaural', 'Childhood Friend', 'Ear Cleaning', 'Healing', 'Lovey Dovey/Sweet Love'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/eng/work/=/product_id/RE228866.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testEcchiEng()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testEcchiEng.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/ecchi-eng/work/=/product_id/RE144678.html');
        $this->assertEquals('NEKOPARA vol.1', $metadata->title);
        $this->assertEquals('Circle: NEKO WORKs' . PHP_EOL . 'Chocolat and Vanilla star in a rich adult eroge series with E-mote system and animated H scenes', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ145000/RJ144678_img_main.jpg', $metadata->image);
        $this->assertEquals(['Love Comedy/Romcom', 'Master and Servant', 'Moe', 'Nekomimi (Cat Ears)'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/ecchi-eng/work/=/product_id/RE144678.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testSPLink()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testHome.html');
        // SP版（touch）のURLのテストだがリゾルバ側でURLから-touchを削除してPC版を取得するので、PC版の内容を使用する

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/home-touch/work/=/product_id/RJ221761.html');
        $this->assertEquals('ひつじ、数えてあげるっ', $metadata->title);
        $this->assertEquals('サークル名: Butterfly Dream' . PHP_EOL . '眠れないあなたに彼女が羊を数えてくれる音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ222000/RJ221761_img_main.jpg', $metadata->image);
        $this->assertEquals(['ほのぼの', 'バイノーラル/ダミヘ', '恋人同士', '日常/生活', '癒し'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/home/work/=/product_id/RJ221761.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testShortLink()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testHome.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://dlsite.jp/howtw/RJ221761.html');
        $this->assertEquals('ひつじ、数えてあげるっ', $metadata->title);
        $this->assertEquals('サークル名: Butterfly Dream' . PHP_EOL . '眠れないあなたに彼女が羊を数えてくれる音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ222000/RJ221761_img_main.jpg', $metadata->image);
        $this->assertEquals(['ほのぼの', 'バイノーラル/ダミヘ', '恋人同士', '日常/生活', '癒し'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://dlsite.jp/howtw/RJ221761.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testOldAffiliateLink()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testHome.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/home/dlaf/=/link/work/aid/eai04191/id/RJ221761.html');
        $this->assertEquals('ひつじ、数えてあげるっ', $metadata->title);
        $this->assertEquals('サークル名: Butterfly Dream' . PHP_EOL . '眠れないあなたに彼女が羊を数えてくれる音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ222000/RJ221761_img_main.jpg', $metadata->image);
        $this->assertEquals(['ほのぼの', 'バイノーラル/ダミヘ', '恋人同士', '日常/生活', '癒し'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/home/work/=/product_id/RJ221761.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testSnsAffiliateLink()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testHome.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/home/dlaf/=/t/s/link/work/aid/eai04191/id/RJ221761.html');
        $this->assertEquals('ひつじ、数えてあげるっ', $metadata->title);
        $this->assertEquals('サークル名: Butterfly Dream' . PHP_EOL . '眠れないあなたに彼女が羊を数えてくれる音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ222000/RJ221761_img_main.jpg', $metadata->image);
        $this->assertEquals(['ほのぼの', 'バイノーラル/ダミヘ', '恋人同士', '日常/生活', '癒し'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/home/work/=/product_id/RJ221761.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testAffiliateLink()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testHome.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/home/dlaf/=/t/t/link/work/aid/eai04191/id/RJ221761.html');
        $this->assertEquals('ひつじ、数えてあげるっ', $metadata->title);
        $this->assertEquals('サークル名: Butterfly Dream' . PHP_EOL . '眠れないあなたに彼女が羊を数えてくれる音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ222000/RJ221761_img_main.jpg', $metadata->image);
        $this->assertEquals(['ほのぼの', 'バイノーラル/ダミヘ', '恋人同士', '日常/生活', '癒し'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/home/work/=/product_id/RJ221761.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testAffiliateUrl()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testHome.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('http://www.dlsite.com/home/dlaf/=/aid/eai04191/url/https%3A%2F%2Fwww.dlsite.com%2Fhome%2Fwork%2F=%2Fproduct_id%2FRJ221761.html');
        $this->assertEquals('ひつじ、数えてあげるっ', $metadata->title);
        $this->assertEquals('サークル名: Butterfly Dream' . PHP_EOL . '眠れないあなたに彼女が羊を数えてくれる音声です。', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/doujin/RJ222000/RJ221761_img_main.jpg', $metadata->image);
        $this->assertEquals(['ほのぼの', 'バイノーラル/ダミヘ', '恋人同士', '日常/生活', '癒し'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/home/work/=/product_id/RJ221761.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }

    public function testAffiliateBadUrl()
    {
        $this->createResolver(DLsiteResolver::class, '');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('アフィリエイト先のリンクがDLsiteのタイトルではありません: https://www.dlsite.com/home/');

        $this->resolver->resolve('http://www.dlsite.com/home/dlaf/=/aid/eai04191/url/https%3A%2F%2Fwww.dlsite.com%2Fhome%2F');
    }

    public function testHTMLdescription()
    {
        $responseText = $this->fetchSnapshot(__DIR__ . '/../../fixture/DLsite/testHTMLdescription.html');

        $this->createResolver(DLsiteResolver::class, $responseText);

        $metadata = $this->resolver->resolve('https://www.dlsite.com/books/work/=/product_id/BJ123822.html');
        $this->assertEquals('獣○彼女カタログ', $metadata->title);
        $this->assertEquals('著者: チキコ / MUJIN編集部' . PHP_EOL . '【DLsite.com独占販売】 エロ漫画界騒然、1冊まるごと獣○オンリー単行本! 人間チ×ポは出てきませんっ!!', $metadata->description);
        $this->assertEquals('https://img.dlsite.jp/modpub/images2/work/books/BJ124000/BJ123822_img_main.jpg', $metadata->image);
        $this->assertEquals(['フェラチオ', 'メイド', '中出し', '処女', '制服', '巨乳/爆乳', '巫女', '断面図', '水着', '異種姦', '複数プレイ/乱交', '褐色/日焼け', '軍服'], $metadata->tags);
        if ($this->shouldUseMock()) {
            $this->assertSame('https://www.dlsite.com/books/work/=/product_id/BJ123822.html', (string) $this->handler->getLastRequest()->getUri());
        }
    }
}
