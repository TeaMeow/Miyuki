<p align="center">
  x
</p>
<p align="center">
  <i>x</i>
</p>

&nbsp;

# Miyuki [![Build Status](https://api.travis-ci.org/TeaMeow/Miyuki.svg?branch=master)](https://travis-ci.org/TeaMeow/Miyuki)

美由紀是一個基於 PHP 且運用 Imagick 套件的影像處理類別，**你必須安裝 Imagick**，

你可以給予一個影像路徑，接下來開始對該影像裁切、縮小、縮圖等。

&nbsp;

# 特色

1. 支援保持長寬比。

2. 基於 Imagick 原生函式。

3. 支援動態畫質調整，依照圖片大小更佳壓縮。

4. 內建圖片檢查函式，無需另外撰寫。

5. 可自動將圖片存於暫存資料夾。

&nbsp;

# 教學

&nbsp;

# 範例

假設你有個圖片在 `test.png`，那麼你就要先輸入這個圖片。

```php
$miyuki = new Miyuki('test.png');

/** 或是你也可以這樣 */
$miyuki = new Miyuki();
$miyuki->create('test.png');
```

&nbsp;

接著對這張圖片進行縮圖，並且變更畫質至 `0.5`。

```php
$miyuki->setQuality(0.5)
       ->resize(1280, 720);
```

&nbsp;

然後將這張圖片儲存在暫存資料夾，並取得路徑。

```php
$path = $miyuki->write(false);
```

&nbsp;

接下來你會得到儲存後的圖片路徑。

```
/tmp/ZGYzqA.png
```

&nbsp;

# 可參考文件

無