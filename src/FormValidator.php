<?php

declare(strict_types=1);

namespace Raihan\FormValidator;

use Exception;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Warning\Warning;
use Egulias\EmailValidator\Exception\InvalidEmail;
use Egulias\EmailValidator\Validation\EmailValidation;


class FormValidator
{


   /**
    * The config.
    *
    * @var array
    */
   protected $config;

   /**
    * The data under validation.
    *
    * @var array
    */
   protected $data;

   /**
    * The rules to be applied to the data.
    *
    * @var array
    */
   protected $rules;

   /**
    * The failed validation rules.
    *
    * @var array
    */
   protected $failedRules = [];

   /**
    * The message bag instance.
    *
    * @var 
    */
   protected $messages = [];


   /**
    * The size related validation rules.
    *
    * @var array
    */
   protected $failed = false;


   /**
    * array $config
    */
   public function __construct(array $config = [])
   {
      $this->config = $config;
   }

   /**
    * array $inputs
    */
   public function request(array $inputs): self
   {

      $this->data = $inputs;
      return $this;
   }

   /**
    * strng $name
    * array|string $rule
    */
   public function rule(string $name, array|string $rule): self
   {

      $this->rules = [$name => $rule];
      return $this;
   }
   /**
    * strng $name
    * array|string $rule
    */
   public function rules(array $rules): self
   {

      $this->rules = $rules;
      return $this;
   }

   /**
    * boolean
    */
   public function fails(): bool
   {
      if ($this->failed) {
         return true;
      } else {
         return false;
      }
   }

   /**
    * boolean
    */
   public function passed(): bool
   {
      if (!$this->failed) {
         return true;
      } else {
         return false;
      }
   }

   /**
    * array
    */
   public function errors(string $inputName = null): array
   {

      if ($inputName == null) {
         return $this->messages;
      } else {
         $allErrors = $this->messages;
         return $allErrors[$inputName];
      }
   }

   /**
    * array
    */
   public function errorsFirst(string $inputName = null): string|array
   {

      if ($inputName == null) {
         $takeFirstMessage = [];
         foreach ($this->messages as $key => $value) {
            $takeFirstMessage[$key] = array_values($value)[0];
         }

         return $takeFirstMessage;
      } else {
         $allErrors = $this->messages;
         return array_values($allErrors[$inputName])[0];
      }
   }


   public function validate(): self
   {
      $inputsData = $this->data;
      $rules = $this->rules;



      try {

         if (!empty($rules) && !empty($inputsData)) {


            $validateMessageList = [];
            foreach ($rules as $ruleKey => $ruleValue) {



               if (array_key_exists($ruleKey, $inputsData)) {
                  $inputsDataByKey = $inputsData[$ruleKey];
               } else {
                  $inputsDataByKey = '';
               }


               $validateMessageList[$ruleKey] = $this->makeValidation($ruleValue, $inputsDataByKey, $ruleKey);
            }

            $this->messages = $validateMessageList;
         }

         return $this;
      } catch (Exception $e) {
         $this->messages = $e->getMessage();
         return $this;
      }
   }


   private function makeValidation(string|array $inputRules, string $inputsData, string $inputKey): array
   {

      $inputRulesArr = [];
      if (is_array($inputRules)) {
         $inputRulesArr = $inputRules;
      } elseif (!empty($inputRules)) {
         $inputRulesArr = explode('|', $inputRules);
      }

      // dd($inputKey, $inputRulesArr);

      $inputValidationMessage = [];
      if (count($inputRulesArr) > 0) {
         foreach ($inputRulesArr as $rule) {

            // $return_value = [];
            if (strpos($rule, ':') !== false) {
               $ruleContainValue = explode(':', $rule);
               $rule = $ruleContainValue[0];
               $argLength = $ruleContainValue[1];
            }

            $return_value = match ($rule) {
               'required' => $this->validationByType('required', $inputsData),
               'email' => $this->validationByType('email', $inputsData),
               'numeric' => $this->validationByType('numeric', $inputsData),
               'min' => $this->validationByType('min', $inputsData, $argLength),
               'max' => $this->validationByType('max', $inputsData, $argLength),
            };

            $inputValidationMessage[$rule] = $return_value;
         }
      }


      return $inputValidationMessage;
   }



   private function validationByType(string $rulesType, string $inputData, $argLength = null): string
   {

      if ($rulesType == 'required' && empty($inputData)) {
         $this->failed = true;
         return 'This field is required!';
      } elseif ($rulesType == 'email' && (filter_var($inputData, FILTER_VALIDATE_EMAIL) == false)) {
         $this->failed = true;
         return 'This is not a valid email address!';
      } elseif ($rulesType == 'numeric' && is_numeric($inputData)) {
         $this->failed = true;
         return 'This is not a valid numeric value!';
      } elseif ($rulesType == 'min' && (strlen($inputData) < $argLength)) {
         $this->failed = true;
         return 'Length is too short!';
      } elseif ($rulesType == 'max' && (strlen($inputData) > $argLength)) {
         $this->failed = true;
         return 'Length is too long!';
      } else {
         return '';
      }
   }
} // Class End