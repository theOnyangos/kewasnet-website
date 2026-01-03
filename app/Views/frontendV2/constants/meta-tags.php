<?php $uri = service('uri'); $segments = $uri->getSegments(); ?>

<meta charset="utf-8">
<title><?= $this->renderSection("title") ?> </title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="keywords" content="<?= $this->renderSection("keywords") ?? "KEWASNET, Kenya Water and Sanitation Civil Society Network, Water, Sanitation, Civil Society, Network, Kenya, Nairobi, Kisumu, Mombasa, Nakuru, Eldoret, Kisii, Kakamega, Bungoma, Kericho, Nyeri, Meru, Thika, Malindi, Kitale, Garissa, Embu, Nanyuki, Machakos, Ruiru, Kilifi, Vihiga, Mumias, Homa Bay, Naivasha, Narok, Busia, Kajiado, Kapenguria, Nyahururu, Migori, Kitui, Wajir, Mandera, Marsabit, Lamu, Taita Taveta, Taveta, Siaya, Isiolo, Nandi, Baringo, Laikipia, Tana River, Uasin Gishu, West Pokt" ?>" />
<meta name="description" content="<?= $description ?>">
<meta name="author" content="Dennis Otieno Email: denonyango@gmail.com">
<meta name="base-url" content="<?= base_url(); ?>">
<link rel="shortcut icon" href="<?= base_url("mission.jpg") ?>">
<link rel="canonical" href="<?= current_url() ?>">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="<?= $this->renderSection("seo_title") ?? $this->renderSection("title") ?>">
<meta property="og:description" content="<?= $this->renderSection("description") ?? $description ?>">
<meta property="og:image" content="<?= $this->renderSection("image_url") ?? base_url('hero.png') ?>">
<meta property="og:url" content="<?= current_url() ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="KEWASNET">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $this->renderSection("seo_title") ?? $this->renderSection("title") ?>">
<meta name="twitter:description" content="<?= $this->renderSection("description") ?? $description ?>">
<meta name="twitter:image" content="<?= $this->renderSection("image_url") ?? base_url('hero.png') ?>">