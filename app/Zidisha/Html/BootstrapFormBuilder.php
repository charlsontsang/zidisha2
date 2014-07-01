<?php

namespace Zidisha\Html;

use Illuminate\Config\Repository as Config;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Html\FormBuilder;
use Illuminate\Session\SessionManager as Session;

class BootstrapFormBuilder
{
    /**
     * Illuminate HtmlBuilder instance.
     *
     * @var \Illuminate\Html\FormBuilder
     */
    protected $html;

    /**
     * Illuminate FormBuilder instance.
     *
     * @var \Illuminate\Html\FormBuilder
     */
    protected $form;

    /**
     * Illuminate Repository instance.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Illuminate SessionManager instance.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    protected $translationDomain = '';

    public function __construct(HtmlBuilder $html, FormBuilder $form, Config $config, Session $session)
    {
        $this->html = $html;
        $this->form = $form;
        $this->config = $config;
        $this->session = $session;
    }

    /**
     * Open a form while passing a model and the routes for storing or updating
     * the model. This will set the correct route along with the correct
     * method.
     *
     * @param  array $options
     * @return string
     */
    public function open(array $options = [])
    {
        $this->translationDomain = array_get($options, 'translationDomain', '');
        unset($options['name']);

        return $this->form->open($options);
    }

    /**
     * Create a new model based form builder.
     *
     * @param  mixed $model
     * @param  array $options
     * @return string
     */
    public function model($model, array $options = array())
    {
        return $this->form->model($model, $options);
    }

    public function populate($form, array $options = array())
    {
        return $this->form->model($form->getData(), $options);
    }

    /**
     * Close the current form.
     *
     * @return string
     */
    public function close()
    {
        return $this->form->close();
    }

    /**
     * Create a Bootstrap text field input.
     *
     * @param  string $name
     * @param  string $value
     * @param  array $options
     * @return string
     */
    public function text($name, $value = null, $options = [])
    {
        return $this->input('text', $name, $value, $options);
    }

    public function file($name, $options = [])
    {
        return $this->input('file', $name, null, $options);
    }

    /**
     * Create a Bootstrap email field input.
     *
     * @param  string $name
     * @param  string $value
     * @param  array $options
     * @return string
     */
    public function email($name = 'email', $value = null, $options = [])
    {
        return $this->input('email', $name, $value, $options);
    }

    /**
     * Create a Bootstrap textarea field input.
     *
     * @param  string $name
     * @param  string $value
     * @param  array $options
     * @return string
     */
    public function textarea($name, $value = null, $options = [])
    {
        return $this->input('textarea', $name, $value, $options);
    }

    public function select($name, $list = [], $selected = null, $options = [])
    {
        $label = array_get($options, 'label', $this->translationDomain ? $this->translationDomain . '.' . $name : $name);
        unset($options['label']);
        $label = \Lang::get($label);

        $options = $this->getFieldOptions($options);
        $wrapperOptions = ['class' => $this->getRightColumnClass()];

        $inputElement = $this->form->select($name, $list, $selected, $options);

        $groupElement = '<div ' . $this->html->attributes($wrapperOptions) . '>' . $inputElement . $this->getFieldError(
                $name
            ) . '</div>';

        return $this->getFormGroup($name, $label, $groupElement);
    }

    /**
     * Create a Bootstrap password field input.
     *
     * @param  string $name
     * @param  array $options
     * @return string
     */
    public function password($name, $options = [])
    {
        return $this->input('password', $name, null, $options);
    }

    /**
     * Create a checkbox input field.
     *
     * @param  string $name
     * @param  mixed $value
     * @param  bool $checked
     * @param  array $options
     * @return string
     */
    public function checkbox($name, $value = 1, $checked = null, $options = array())
    {
        $label = array_get($options, 'label', $this->translationDomain ? $this->translationDomain . '.' . $name : $name);
        unset($options['label']);
        $label = \Lang::get($label);

        return '<div class="checkbox"><label>' . $this->form->checkbox(
            $name,
            $value,
            $checked,
            $options
        ) . $label . '</label></div>';
    }

    public function radio($name, $value = 1, $checked = null, $options = array())
    {
        $label = array_get($options, 'label', $this->translationDomain ? $this->translationDomain . '.' . $name : $name);
        unset($options['label']);
        $label = \Lang::get($label);

        return '<div class="radio"><label>' . $this->form->radio(
            $name,
            $value,
            $checked,
            $options
        ) . $label . '</label></div>';
    }

    public function radios($names, $value = 1, $checked = null, $options = array())
    {
        $nameArray = explode(",", $names);
        $groupName = $nameArray[0];
        $view = $this->form->label($groupName);
        array_shift($nameArray);
        foreach ($nameArray as $name) {
            $label = array_get($options, 'label', $this->translationDomain ? $this->translationDomain . '.' . $name : $name);
            unset($options['label']);
            $label = \Lang::get($label);

            $view = $view . '<div class="radio"><label>' . $this->form->radio(
                    $groupName,
                    $value,
                    $checked,
                    $options
                ) . $label . '</label></div>';
        }
        return $view;
    }

    /**
     * Create a Bootstrap label.
     *
     * @param  string $name
     * @param  string $value
     * @param  array $options
     * @return string
     */
    public function label($name, $value = null, $options = [])
    {
        $options = $this->getLabelOptions($options);

        return $this->form->label($name, \Lang::get($value), $options);
    }

    /**
     * Create a Bootstrap submit button.
     *
     * @param  string $value
     * @param  array $options
     * @return string
     */
    public function submit($value = null, $options = [])
    {
        $options = ['name' => $value] + $options;

        $value = $this->translationDomain ? $this->translationDomain . '.' . $value : $value;
        $options = array_merge(['class' => 'btn btn-primary'], $options);

        return $this->form->submit(\Lang::get($value), $options);
    }

    /**
     * Create a hidden input field.
     *
     * @param  string $name
     * @param  string $value
     * @param  array $options
     * @return string
     */
    public function hidden($name, $value = null, $options = array())
    {
        return $this->form->input('hidden', $name, $value, $options);
    }

    /**
     * Create the input group for an element with the correct classes for errors.
     *
     * @param  string $type
     * @param  string $name
     * @param  string $value
     * @param  array $options
     * @return string
     */
    protected function input($type, $name, $value = null, $options = [])
    {
        $label = array_get($options, 'label', $this->translationDomain ? $this->translationDomain . '.' . $name : $name);
        unset($options['label']);
        
        $options = $this->getFieldOptions($options);
        $wrapperOptions = ['class' => $this->getRightColumnClass()];

        $description = '';
        if (isset($options['description'])) {
            $description = '<span class="help-block">' . $options['description'] . '</span>';
            unset($options['description']);
        }
        
        $prepend = '';
        if (isset($options['prepend'])) {
            $prepend = '<span class="input-group-addon">' . $options['prepend'] . '</span>';
            unset($options['prepend']);
            $wrapperOptions['class'] = $wrapperOptions['class'] . ' input-group';
        }

        $append = '';
        if (isset($options['append'])) {
            $append = '<span class="input-group-addon">' . $options['append'] . '</span>';
            unset($options['append']);
            $wrapperOptions['class'] = $wrapperOptions['class'] . ' input-group';
        }
        
        $inputElement = $type == 'password' ? $this->form->password($name, $options) : $this->form->{$type}(
            $name,
            $value,
            $options
        );

        $groupElement = '<div ' . $this->html->attributes($wrapperOptions) . '>' . $prepend . $inputElement . $append . '</div>';
        $groupElement .= $this->getFieldError($name);

        return $this->getFormGroup($name, $label, $description . $groupElement);
    }

    /**
     * Get a form group comprised of a label, form element and errors.
     *
     * @param  string $name
     * @param  string $label
     * @param  string $element
     * @return string
     */
    protected function getFormGroup($name, $label, $element)
    {
        $options = $this->getFormGroupOptions($name);
        $label = $label ? $this->label($name, $label) : '';

        return '<div ' . $this->html->attributes($options) . '>' . $label . $element . '</div>';
    }

    /**
     * Merge the options provided for a form group with the default options
     * required for Bootstrap styling.
     *
     * @param  string $name
     * @param  array $options
     * @return array
     */
    protected function getFormGroupOptions($name, $options = [])
    {
        $class = trim('form-group ' . $this->getFieldErrorClass($name));

        return array_merge(['class' => $class], $options);
    }

    /**
     * Merge the options provided for a field with the default options
     * required for Bootstrap styling.
     *
     * @param  array $options
     * @return array
     */
    protected function getFieldOptions($options = [])
    {
        return array_merge(['class' => 'form-control'], $options);
    }

    /**
     * Merge the options provided for a label with the default options
     * required for Bootstrap styling.
     *
     * @param  array $options
     * @return array
     */
    protected function getLabelOptions($options = [])
    {
        $class = trim('control-label ' . $this->getLeftColumnClass());

        return array_merge(['class' => $class], $options);
    }

    /**
     * Get the column class for the left class of a horizontal form.
     *
     * @return string
     */
    protected function getLeftColumnClass()
    {
        return $this->config->get('bootstrap-form::left_column') ? : '';
    }

    /**
     * Get the column class for the right class of a horizontal form.
     *
     * @return string
     */
    protected function getRightColumnClass()
    {
        return $this->config->get('bootstrap-form::right_column') ? : '';
    }

    /**
     * Get the MessageBag of errors that is populated by the
     * validator.
     *
     * @return \Illuminate\Support\MessageBag
     */
    protected function getErrors()
    {
        return $this->session->get('errors');
    }

    /**
     * Get the first error for a given field, using the provided
     * format, defaulting to the normal Bootstrap 3 format.
     *
     * @param  string $field
     * @param  string $format
     * @return string
     */
    protected function getFieldError($field, $format = '<span class="help-block">:message</span>')
    {
        if (!$this->getErrors()) {
            return;
        }

        return $this->getErrors()->first($field, $format);
    }

    /**
     * Return the error class if the given field has associated
     * errors, defaulting to the normal Bootstrap 3 error class.
     *
     * @param  string $field
     * @param  string $class
     * @return string
     */
    protected function getFieldErrorClass($field, $class = 'has-error')
    {
        return $this->getFieldError($field) ? $class : null;
    }
}
