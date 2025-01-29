<?php

namespace Vanacode\Validator\Traits;

use Vanacode\Validator\Validator;

/**
 * use with Vanacode\Support\Traits\DynamicClassTrait
 */
trait ValidatorPropertyTrait
{
    protected Validator $validator;

    /**
     * initialize validator property with own model property
     *
     * if $validator argument is null, make validator instance dynamically based caller sub folders first match
     * Set validator property
     * set validator $model property by caller $model property if it set,
     * otherwise make model instance dynamically based caller sub folders first match and then set it to validator
     */
    public function initializeValidator(?Validator $validator = null, array $data = []): self
    {
        $this->setValidatorBy($validator, $data);

        $model = $this->getModelBy($data['model_data'] ?? []);

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
     * Set validator property
     *
     * if $validator argument is not null
     * otherwise make validator instance dynamically based caller sub folders first match and set it
     */
    public function setValidatorBy(?Validator $validator, array $data = []): self
    {
        $validator = $validator ?? $this->makeValidator($data);

        return $this->setValidator($validator);
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * make validator instance dynamically based caller sub folders first match
     */
    public function makeValidator(array $data = []): Validator
    {
        if (! array_key_exists('default', $data)) {
            $data['default'] = static::validatorClass();
        }

        return $this->makeClassDynamically('Validators', 'Validator', $data);
    }

    /**
     * Default validator class
     */
    public static function validatorClass(): string
    {
        return Validator::class;
    }
}
