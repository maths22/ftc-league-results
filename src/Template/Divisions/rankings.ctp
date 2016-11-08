<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('All Team Rankings'), ['controller' => 'Teams', 'action' => 'rankings']) ?> </li>
        <li><?= $this->Html->link(__('League Rankings'), ['controller' => 'Leagues', 'action' => 'rankings', $division->league->id]) ?> </li>
    </ul>
</nav>
<div class="divisions view large-9 medium-8 columns content">
    <h3>
        <?= $division->has('league') ? $this->Html->link($division->league->name, ['controller' => 'Leagues', 'action' => 'rankings', $division->league->id]) : '' ?>
        -
        <?= h($division->name) ?> Rankings
    </h3>
    <div class="related table-responsive">
        <?= $this->element('rankings', ['showDivisions' => false, 'showLeagues' => false]); ?>
    </div>
</div>
