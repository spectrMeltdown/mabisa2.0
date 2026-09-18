module.exports = {
  env: {
    browser: true,
    es2020: true, // Change from es2021 to es2020
  },
  extends: ['eslint:recommended', 'plugin:prettier/recommended'],
  parserOptions: {
    ecmaVersion: 2020, // Set the ECMAScript version to 2020
    sourceType: 'module',
  },
  globals: {
    loading: 'readonly',       
    toggleLoading: 'readonly',
    $: 'readonly',
  },
  rules: {
    'prettier/prettier': 'error',
    'no-undef': 'warn',  // You can make this 'warn' if you don't want errors for undefined variables
  },  
};
