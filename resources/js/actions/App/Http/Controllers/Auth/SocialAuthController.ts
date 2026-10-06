import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:29
* @route '/auth/{provider}/redirect'
*/
export const redirect = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirect.url(args, options),
    method: 'get',
})

redirect.definition = {
    methods: ["get","head"],
    url: '/auth/{provider}/redirect',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:29
* @route '/auth/{provider}/redirect'
*/
redirect.url = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { provider: args }
    }

    if (Array.isArray(args)) {
        args = {
            provider: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        provider: args.provider,
    }

    return redirect.definition.url
            .replace('{provider}', parsedArgs.provider.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:29
* @route '/auth/{provider}/redirect'
*/
redirect.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:29
* @route '/auth/{provider}/redirect'
*/
redirect.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: redirect.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:29
* @route '/auth/{provider}/redirect'
*/
const redirectForm = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:29
* @route '/auth/{provider}/redirect'
*/
redirectForm.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::redirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:29
* @route '/auth/{provider}/redirect'
*/
redirectForm.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: redirect.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

redirect.form = redirectForm

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::callback
* @see app/Http/Controllers/Auth/SocialAuthController.php:96
* @route '/auth/{provider}/callback'
*/
export const callback = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: callback.url(args, options),
    method: 'get',
})

callback.definition = {
    methods: ["get","head"],
    url: '/auth/{provider}/callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::callback
* @see app/Http/Controllers/Auth/SocialAuthController.php:96
* @route '/auth/{provider}/callback'
*/
callback.url = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { provider: args }
    }

    if (Array.isArray(args)) {
        args = {
            provider: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        provider: args.provider,
    }

    return callback.definition.url
            .replace('{provider}', parsedArgs.provider.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::callback
* @see app/Http/Controllers/Auth/SocialAuthController.php:96
* @route '/auth/{provider}/callback'
*/
callback.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: callback.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::callback
* @see app/Http/Controllers/Auth/SocialAuthController.php:96
* @route '/auth/{provider}/callback'
*/
callback.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: callback.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::callback
* @see app/Http/Controllers/Auth/SocialAuthController.php:96
* @route '/auth/{provider}/callback'
*/
const callbackForm = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: callback.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::callback
* @see app/Http/Controllers/Auth/SocialAuthController.php:96
* @route '/auth/{provider}/callback'
*/
callbackForm.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: callback.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::callback
* @see app/Http/Controllers/Auth/SocialAuthController.php:96
* @route '/auth/{provider}/callback'
*/
callbackForm.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: callback.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

callback.form = callbackForm

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::linkRedirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:62
* @route '/settings/connections/{provider}/redirect'
*/
export const linkRedirect = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: linkRedirect.url(args, options),
    method: 'get',
})

linkRedirect.definition = {
    methods: ["get","head"],
    url: '/settings/connections/{provider}/redirect',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::linkRedirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:62
* @route '/settings/connections/{provider}/redirect'
*/
linkRedirect.url = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { provider: args }
    }

    if (Array.isArray(args)) {
        args = {
            provider: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        provider: args.provider,
    }

    return linkRedirect.definition.url
            .replace('{provider}', parsedArgs.provider.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::linkRedirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:62
* @route '/settings/connections/{provider}/redirect'
*/
linkRedirect.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: linkRedirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::linkRedirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:62
* @route '/settings/connections/{provider}/redirect'
*/
linkRedirect.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: linkRedirect.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::linkRedirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:62
* @route '/settings/connections/{provider}/redirect'
*/
const linkRedirectForm = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: linkRedirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::linkRedirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:62
* @route '/settings/connections/{provider}/redirect'
*/
linkRedirectForm.get = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: linkRedirect.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\SocialAuthController::linkRedirect
* @see app/Http/Controllers/Auth/SocialAuthController.php:62
* @route '/settings/connections/{provider}/redirect'
*/
linkRedirectForm.head = (args: { provider: string | number } | [provider: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: linkRedirect.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

linkRedirect.form = linkRedirectForm

const SocialAuthController = { redirect, callback, linkRedirect }

export default SocialAuthController