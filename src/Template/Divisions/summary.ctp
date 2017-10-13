<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('All Team Rankings'), ['controller' => 'Teams', 'action' => 'rankings']) ?> </li>
    </ul>
</nav>
<div class="divisions index large-9 medium-8 columns content">
    <h3><?= __('Leagues') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= __('League') ?></th>
                <th scope="col"><?= __('Division') ?></th>
<!--                <th scope="col">--><?//= __('Events Completed') ?><!--</th>-->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($divisions as $division): ?>
            <tr>
                <td><?= $division->has('league') ? $this->Html->link($division->league->name, ['controller' => 'Leagues', 'action' => 'rankings', $division->league->id]) : '' ?></td>
                <td><?= $this->Html->link($division->name, ['controller' => 'Divisions', 'action' => 'rankings', $division->id])     ?></td>
<!--                <td>--><?//= count($division->events) ?><!--</td>-->
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
