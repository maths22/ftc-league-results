
<?php if (!empty($rankings)): ?>
    <table cellpadding="0" cellspacing="0" class="table">
        <tr>
            <th scope="col"><?= __('Number') ?></th>
            <th scope="col"><?= __('Name') ?></th>

            <?php if($showDivisions): ?>

                <th scope="col">
                <?php if($showLeagues): ?>
                    <?= __('League') ?> -
                <?php endif; ?>
                <?= __('Division') ?></th>
            <?php endif; ?>
            <th scope="col"><?= __('QP') ?></th>
            <th scope="col"><?= __('RP') ?></th>
            <th scope="col"><?= __('High Score') ?></th>
            <th scope="col"><?= __('Matches Played') ?></th>
        </tr>
        <?php foreach ($rankings as $ranking): ?>
            <tr>
                <td>
                    <?= $this->Html->link($ranking['team_id'], ['controller' => 'Teams', 'action' => 'summary', $ranking['team_id']]) ?>
                </td>
                <td><?= h($ranking['team_name']) ?></td>
                <?php if($showDivisions): ?>
                    <td>
                        <?php if($showLeagues): ?>
                        <?= $ranking['division']->league->has('name') ? $this->Html->link($ranking['division']->league->name, ['controller' => 'Leagues', 'action' => 'rankings', $ranking['division']->league->id]) : '' ?>
                        - <br>
                        <?php endif; ?>
                        <?= $ranking['division']->has('name') ? $this->Html->link($ranking['division']->name, ['controller' => 'Divisions', 'action' => 'rankings', $ranking['division']->id]) : '' ?>
                    </td>
                <?php endif; ?>
                <td><?= $this->Number->format($ranking['qp']) ?></td>
                <td><?= $this->Number->format($ranking['rp']) ?></td>
                <td><?= $this->Number->format(isset($ranking['scores'][0]) ? $ranking['scores'][0] : '') ?></td>
                <td><?= $this->Number->format($ranking['matches_played']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>