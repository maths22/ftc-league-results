<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('All Team Rankings'), ['controller' => 'Teams', 'action' => 'rankings']) ?> </li>
        <?php foreach ($league->divisions as $division): ?>
        <li><?= $this->Html->link(h($division->name . ' Rankings'), ['controller' => 'Divisions', 'action' => 'rankings', $division->id]) ?> </li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="leagues view large-9 medium-8 columns content">
    <h3><?= h($league->name) ?> Rankings</h3>

    <div class="related table-responsive">
        <?= $this->element('rankings', ['showDivisions' => true, 'showLeagues' => false]); ?>
    </div>
</div>
