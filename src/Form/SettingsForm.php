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

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SettingsForm extends ConfigFormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'asm_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function getEditableConfigNames()
    {
        return ['asm.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('asm.settings');

        $form['asm_url'] = [
            '#type'          => 'textfield',
            '#title'         => 'Shelter Manager Url',
            '#default_value' => $config->get('asm_url')
        ];
        $form['asm_user'] = [
            '#type'          => 'textfield',
            '#title'         => 'Username',
            '#default_value' => $config->get('asm_user')
        ];
        $form['asm_pass'] = [
            '#type'          => 'textfield',
            '#title'         => 'Password',
            '#default_value' => $config->get('asm_pass')
        ];
        $form['asm_proxy'] = [
            '#type'          => 'checkbox',
            '#title'         => 'Proxy Images',
            '#default_value' => $config->get('asm_proxy') ? true : false,
            '#description'   => 'If Shelter Manager is behind a firewall, you can check this to have the ASM module proxy image requests'
        ];
        $form['asm_breadcrumb'] = [
            '#type'          => 'textfield',
            '#title'         => 'Breadcrumbs',
            '#default_value' => $config->get('asm_breadcrumb'),
            '#description'   => 'Enter node IDs, separated by commas, to be used as the base breadcrumb for ASM routes'
        ];
        $form['asm_route'] = [
            '#type'          => 'textfield',
            '#title'         => 'Base Route',
            '#default_value' => $config->get('asm_route'),
            '#description'   => "Specify an alias to use as the base for this module's routes"
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->config('asm.settings')
             ->set('asm_url',        $form_state->getValue('asm_url' ))
             ->set('asm_user',       $form_state->getValue('asm_user'))
             ->set('asm_pass',       $form_state->getValue('asm_pass'))
             ->set('asm_proxy',      $form_state->getValue('asm_proxy') ? 1 : 0)
             ->set('asm_breadcrumb', $form_state->getValue('asm_breadcrumb'))
             ->set('asm_route',      $form_state->getValue('asm_route'))
             ->save();

        parent::submitForm($form, $form_state);
    }
}
