<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Event Entity
 *
 * @property int $id
 * @property string $type
 * @property int $league_id
 * @property int $division_id
 * @property string $name
 * @property string $slug
 * @property \Cake\I18n\FrozenDate $date
 *
 * @property \App\Model\Entity\League $league
 * @property \App\Model\Entity\Division $division
 * @property \App\Model\Entity\Match[] $matches
 */
class Event extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'slug' => true,
        'type' => true,
        'league_id' => true,
        'division_id' => true,
        'name' => true,
        'date' => true,
        'league' => true,
        'division' => true,
        'matches' => true,
        'ftp_host' => true,
        'ftp_port' => true,
        'ftp_user' => true,
        'ftp_pass' => true,
        'ftp_path' => true,
        'web_url' => true,
    ];

    protected function _getImported()
    {
        return count($this->matches) > 0;
    }

}
