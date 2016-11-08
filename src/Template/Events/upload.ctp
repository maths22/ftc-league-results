<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('All Team Rankings'), ['controller' => 'Teams', 'action' => 'rankings']) ?> </li>
        <li><?= $this->Html->link(__('List Divisions'), ['controller' => 'Divisions', 'action' => 'summary']) ?></li>
    </ul>
</nav>
<div class="event form large-9 medium-8 columns content">
    <?= $this->Form->create($event, ['type' => 'file']) ?>
    <fieldset>
        <legend><?= __('Add Event') ?></legend>
        <?php
            echo $this->Form->input('division_id', ['options' => $divisions]);
            echo $this->Form->input('scoring_dump', ['type' => 'file']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
