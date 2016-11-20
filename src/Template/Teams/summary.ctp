<?php if(!$embed): ?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('All Team Rankings'), ['controller' => 'Teams', 'action' => 'rankings']) ?> </li>
        <li><?= $this->Html->link(__('League Rankings'), ['controller' => 'Leagues', 'action' => 'rankings', $team->division->league->id]) ?> </li>
        <li><?= $this->Html->link(__('Division Rankings'), ['controller' => 'Divisions', 'action' => 'rankings', $team->division->id]) ?> </li>
    </ul>
</nav>
<?php endif; ?>
<div class="teams view <?= $embed?'':'large-9 medium-8' ?> columns content">
    <h3><?= h($team->id . ' - ' . $team->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('League - Division') ?></th>
            <td>
                <?= $team->has('division') ? $this->Html->link($team->division->league->name, ['controller' => 'Leagues', 'action' => 'rankings', $team->division->league->id]) : '' ?>
                -
                <?= $team->has('division') ? $this->Html->link($team->division->name, ['controller' => 'Divisions', 'action' => 'rankings', $team->division->id]) : '' ?>
            </td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Matches') ?></h4>
        <?php if (!empty($team->matches)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Event') ?></th>
                <th scope="col"><?= __('Number') ?></th>
                <th scope="col"><?= __('QP') ?></th>
                <th scope="col"><?= __('RP') ?></th>
                <th scope="col"><?= __('Score') ?></th>
            </tr>
            <?php foreach ($team->matches as $matches): ?>
            <tr>
                <td><?= h($matches->event->name) ?></td>
                <td><?= h($matches->num) ?></td>
                <td><?= h($matches->qp) ?></td>
                <td><?= h($matches->rp) ?></td>
                <td><?= h($matches->score) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
