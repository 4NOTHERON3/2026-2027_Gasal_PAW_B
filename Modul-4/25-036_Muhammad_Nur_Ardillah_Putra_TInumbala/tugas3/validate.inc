<?php
// Semua fungsi validasi menyimpan pesan error ke array $errors[nama_field]

// surname: alfabet, apostrof('), tanda hubung(-)
function validateName(&$errors, $field_list, $field_name)
{
    $pattern = "/^[a-zA-Z'-]+$/"; // format surname (alfabet)
    if (!isset($field_list[$field_name]) || empty($field_list[$field_name]))
        $errors[$field_name] = 'required';
    else if (!preg_match($pattern, $field_list[$field_name]))
        $errors[$field_name] = 'invalid';
}

// email: format alamat email
function validateEmail(&$errors, $field_list, $field_name)
{
    if (!isset($field_list[$field_name]) || trim($field_list[$field_name]) === '')
        $errors[$field_name] = 'required';
    else if (!filter_var($field_list[$field_name], FILTER_VALIDATE_EMAIL))
        $errors[$field_name] = 'invalid';
}

// password: minimal 6 karakter
function validatePassword(&$errors, $field_list, $field_name)
{
    if (!isset($field_list[$field_name]) || $field_list[$field_name] === '')
        $errors[$field_name] = 'required';
    else if (strlen($field_list[$field_name]) < 6)
        $errors[$field_name] = 'invalid (minimum 6 characters)';
}

// address: wajib diisi
function validateAddress(&$errors, $field_list, $field_name)
{
    if (!isset($field_list[$field_name]) || trim($field_list[$field_name]) === '')
        $errors[$field_name] = 'required';
}

// state: harus memilih salah satu (nilai 1 sampai 8)
function validateState(&$errors, $field_list, $field_name)
{
    if (!isset($field_list[$field_name]) || $field_list[$field_name] === '-1')
        $errors[$field_name] = 'required';
    else if (!in_array($field_list[$field_name], array('1','2','3','4','5','6','7','8'), true))
        $errors[$field_name] = 'invalid';
}

// sex: harus memilih salah satu radio button
function validateGender(&$errors, $field_list, $field_name)
{
    if (!isset($field_list[$field_name]))
        $errors[$field_name] = 'required';
    else if (!in_array($field_list[$field_name], array('male', 'female'), true))
        $errors[$field_name] = 'invalid';
}
