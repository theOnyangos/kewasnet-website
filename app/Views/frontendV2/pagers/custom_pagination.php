<?php $pager->setSurroundCount(2); ?>

<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="pagination flex items-center justify-end space-x-2">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="pager__item pager__item--previous">
                <a href="<?= $pager->getPrevious() ?>" class="flex items-center justify-center w-10 h-10 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="pager__item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="flex items-center justify-center w-10 h-10 border rounded-md text-sm font-medium transition-colors <?= $link['active'] ? 'bg-gradient-to-br from-primary via-secondary to-primary text-white' : 'border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-blue-600' ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="pager__item pager__item--next">
                <a href="<?= $pager->getNext() ?>" class="flex items-center justify-center w-10 h-10 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>