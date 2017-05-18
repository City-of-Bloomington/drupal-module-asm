<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/old-licenses/gpl-2.0 GNU/GPL2, see LICENSE
 *
 * This file is part of the ASM drupal module.
 *
 * The ASM module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * The ASM module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the ASM module.  If not, see <https://www.gnu.org/licenses/old-licenses/gpl-2.0/>.
 */
namespace Drupal\asm\Form;

use Drupal\asm\ASMGateway;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AnimalSearchForm extends FormBase
{
    public function getFormId() { return 'asm_animalsearchform'; }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['#method'] = 'get';
        $form['#action'] = $this->url('asm.animals');

        $form['species'] = [
            '#type'          => 'select',
            '#title'         => 'Species',
            '#options'       => ['Cat'=>'Cat', 'Dog'=>'Dog', 'Other'=>'Other'],
            '#default_value' => $form_state->get('species'),
            '#empty_option'  => '',
            '#empty_value'   => ''
        ];

        $form['actions'] = [
            '#type'  => 'actions',
            'submit' => [
                '#type'        => 'submit',
                '#value'       => 'Search',
                '#button_type' => 'primary'
            ]
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $form_state->setRebuild();
    }
}
