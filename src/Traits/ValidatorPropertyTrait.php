<?php

namespace Vanacode\Validator\Traits;

use Vanacode\Support\Exceptions\DynamicClassPropertyException;
use Vanacode\Validator\Validator;

trait ValidatorPropertyTrait
{
    protected Validator $validator;

    protected bool $overSetValidator = true;

    /**
     * if $validator argument is null based $validatorClass argument or validatorClass() method dynamically make validator
     * then set $validator property
     * if $overSetValidator property is true then reset validator $model property by dynamic model match
     * if $overSetValidator property is false and $model property is not set yet in $validator property,
     * then set validator $model property by dynamic model match
     *
     * @throws DynamicClassPropertyException
     */
    public function initializeValidator(?Validator $validator = null, string $validatorClass = '', array $data = []): self
    {
        if ($this->overSetValidator) {
            return $this->initializeValidatorAndOverSet($validator, $validatorClass, $data);
        }

        $this->setValidatorBy($validator, $validatorClass, $data);

        if ($this->validator->isSetModel()) {
            return $this;
        }

        $modelClass = $data['model_class'] ?: $this->validatorModelClass();
        $model = $this->getModelBy($modelClass, $data['model_data'] ?? []);
        if ($model) {
            $this->validator->setModel($model);
        }

        return $this;
    }

    /**
     * if $validator argument is null based $validatorClass argument or validatorClass() method dynamically make validator
     * then set $validator property
     * reset validator $model property by dynamic model match
     *
     * @throws DynamicClassPropertyException
     */
    public function initializeValidatorAndOverSet(?Validator $validator = null, string $validatorClass = '', array $data = []): self
    {
        $this->setValidatorBy($validator, $validatorClass, $data);

        $modelClass = $data['model_class'] ?: $this->validatorModelClass();
        $model = $this->getModelBy($modelClass, $data['model_data'] ?? []);

        if ($model) {
            $this->validator->setModel($model);
        }

        return $this;
    }

    public function setValidator(Validator $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * if validator argument is null based $validatorClass argument or validatorClass() method dynamically make validator
     * then set $validator property
     *
     * @throws DynamicClassPropertyException
     */
    public function setValidatorBy(?Validator $validator, string $validatorClass = '', array $data = []): self
    {
        if (is_null($validator)) {
            $validator = $this->makeValidator($validatorClass, $data);
        }

        return $this->setValidator($validator);
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * based $validatorClass argument or validatorClass() method dynamically make validator
     *
     * @throws DynamicClassPropertyException
     */
    protected function makeValidator(string $validatorClass = '', array $data = []): Validator
    {
        $validatorClass = $validatorClass ?: $this->validatorClass();
        if (! array_key_exists('default', $data)) {
            $data['default'] = $validatorClass;
        }

        return $this->makePropertyInstance('validator', $validatorClass, 'Validators', 'Validator', $data);
    }

    protected function validatorClass(): string
    {
        return Validator::class;
    }

    protected function validatorModelClass(): string
    {
        return $this->validator->modelClass();
    }
}
