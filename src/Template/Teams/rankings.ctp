<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Divisions'), ['controller' => 'Divisions', 'action' => 'summary']) ?></li>
    </ul>
</nav>
<div class="teams index large-9 medium-8 columns content">
    <h3><?= __('Team') ?> Rankings</h3>
    <div class="related table-responsive">
        <?= $this->element('rankings', ['showDivisions' => true, 'showLeagues' => true]); ?>
    </div>
</div>
