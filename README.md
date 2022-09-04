# form-validator
Custom Form Validator Assignment

Git: https://github.com/raihan-chow/form-validator

composer require raihan-chow/form-validator


use Raihan\FormValidator\FormValidator;

$validator = new Validator($config);

Accept User's Inputs
// Accept Input Data
// $inputsData = ['name' => 'Raihan', 'email' => 'raihan.delta@gmail.com'];
$validator->request($inputsData);

Define Rules
$rules = [
  'name' => 'required|min:3|max:60',
  'email' => ['required', 'email']
];

$validator->rules($rules);

or
$validator->rule('email', 'required|email');

Validation
$validator->validate();

Check: Validation
if ($validation->fails() {
// do something
}

if ($validation->passed() {
// do something
}

Getting Errors
Getting all errors
foreach ($validator->errors() as $error) {
// do something
}

Getting all errors of a single input
foreach ($validator->error('email') as $error) {
// do something
}

Getting first error
echo $validator->errorFirst();
//or
echo $validator->errorFirst('email');


