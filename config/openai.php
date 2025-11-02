<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI/AI Model API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify your AI API key. This works with OpenAI API
    | or any OpenAI-compatible API endpoint (GPUStack, LocalAI, etc.)
    |
    */

    'api_key' => env('OPENAI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URI
    |--------------------------------------------------------------------------
    |
    | The base URI for the AI API. For OpenAI use the default.
    | For custom endpoints (GPUStack, LocalAI, etc.) set your endpoint here.
    | Example: http://78.40.111.120:8003/v1
    |
    */

    'base_uri' => env('OPENAI_BASE_URI', 'https://api.openai.com/v1'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Organization ID
    |--------------------------------------------------------------------------
    |
    | If you belong to multiple organizations, you can specify which
    | organization to use for API requests. Leave empty if not needed.
    |
    */

    'organization' => env('OPENAI_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | The default AI model to use for requests. Examples:
    | - OpenAI: gpt-4, gpt-3.5-turbo
    | - GPUStack: gemma3, llama3, mistral
    | - Custom: your-model-name
    |
    */

    'model' => env('OPENAI_MODEL', 'gpt-4'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The maximum time (in seconds) to wait for a response from AI API.
    |
    */

    'timeout' => env('OPENAI_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Max Tokens
    |--------------------------------------------------------------------------
    |
    | The maximum number of tokens to generate in responses.
    |
    */

    'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),

];
