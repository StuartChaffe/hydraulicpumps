<?php
/** @var array $block Block settings, passed in by ACF. */
if ( origin_block_preview( $block ) ) return; // Inserter preview: show preview.jpg instead ?>
<section class="block content-block">
    <div class="container container--s content">
        <InnerBlocks allowedBlocks='["core/heading", "core/paragraph", "core/list", "acf/quote-block", "core/image"]' />
    </div>
</section>
