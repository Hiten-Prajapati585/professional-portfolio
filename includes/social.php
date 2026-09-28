<?php
$social = [
    ['github', 'bi-github', $personal['github']],
    ['linkedin', 'bi-linkedin', $personal['linkedin']],
    ['instagram', 'bi-instagram', $personal['instagram']],
    ['facebook', 'bi-facebook', $personal['facebook']],
    ['twitter', 'bi-twitter-x', $personal['twitter']]
];
foreach ($social as [$label, $icon, $url]) {
    if ($url): ?><a target="_blank" rel="noopener" href="<?= e(clean_url($url)) ?>" aria-label="<?= e($label) ?>"><i class="bi <?= e($icon) ?>"></i></a><?php endif;
                                                                                                                                                }
                                                                                                                                                        ?>