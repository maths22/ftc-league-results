<style>
  .rankings .nowrap {
    white-space:nowrap;
  }
</style>


<?php if (!empty($rankings)): ?>
    <table cellpadding="0" cellspacing="0" class="table rankings">
        <tr>
            <th scope="col" class="nowrap"><?= __('Rank') ?></th>
            <th scope="col" class="nowrap"><?= __('Number') ?></th>
            <th scope="col"><?= __('Name') ?></th>

            <?php if($showDivisions): ?>

                <th scope="col">
                <?php if($showLeagues): ?>
                    <?= __('League') ?> -
                <?php endif; ?>
                <?= __('Division') ?></th>
            <?php endif; ?>
            <th scope="col" class="nowrap"><?= __('QP') ?></th>
            <th scope="col" class="nowrap"><?= __('RP') ?></th>
            <th scope="col"><?= __('High Score') ?></th>
            <th scope="col"><?= __('Matches Played') ?></th>
        </tr>
        <?php foreach ($rankings as $i => $ranking): ?>
            <tr>
                <td class="nowrap"><?= $i + 1 ?></td>
                <td class="nowrap">
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
                <td class="nowrap"><?= $this->Number->format($ranking['qp']) ?></td>
                <td class="nowrap"><?= $this->Number->format($ranking['rp']) ?></td>
                <td class="nowrap"><?= $this->Number->format(isset($ranking['scores'][0]) ? $ranking['scores'][0] : '') ?></td>
                <td class="nowrap"><?= $this->Number->format($ranking['matches_played']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
