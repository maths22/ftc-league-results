<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('All Team Rankings'), ['controller' => 'Teams', 'action' => 'rankings']) ?> </li>
        <li><?= $this->Html->link(__('List Events'), ['controller' => 'Events', 'action' => 'all']) ?></li>
        <li><?= $this->Html->link(__('List Divisions'), ['controller' => 'Divisions', 'action' => 'summary']) ?></li>
    </ul>
</nav>
<div class="event form large-9 medium-8 columns content">
    <h3><?= h($event->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Type') ?></th>
            <td><?= h($event->type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('League') ?></th>
            <td><?= $event->has('league') ? $this->Html->link($event->league->name, ['controller' => 'Leagues', 'action' => 'view', $event->league->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Division') ?></th>
            <td><?= $event->has('division') ? $this->Html->link($event->division->name, ['controller' => 'Divisions', 'action' => 'view', $event->division->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Date') ?></th>
            <td><?= h($event->date) ?></td>
        </tr>
    </table>

    <?= $this->Form->create($event, ['type' => 'file']) ?>
    <fieldset>
        <legend><?= __('Upload Results') ?></legend>
        <?php
            echo $this->Form->input('scoring_dump', ['type' => 'file']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
