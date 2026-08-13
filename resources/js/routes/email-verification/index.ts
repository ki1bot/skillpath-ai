import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::show
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:19
 * @route '/settings/profile/verify-email'
 */
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/settings/profile/verify-email',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::show
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:19
 * @route '/settings/profile/verify-email'
 */
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::show
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:19
 * @route '/settings/profile/verify-email'
 */
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::show
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:19
 * @route '/settings/profile/verify-email'
 */
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Settings\EmailVerificationController::show
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:19
 * @route '/settings/profile/verify-email'
 */
    const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Settings\EmailVerificationController::show
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:19
 * @route '/settings/profile/verify-email'
 */
        showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Settings\EmailVerificationController::show
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:19
 * @route '/settings/profile/verify-email'
 */
        showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::send
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:33
 * @route '/settings/profile/verify-email/send'
 */
export const send = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/settings/profile/verify-email/send',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::send
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:33
 * @route '/settings/profile/verify-email/send'
 */
send.url = (options?: RouteQueryOptions) => {
    return send.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::send
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:33
 * @route '/settings/profile/verify-email/send'
 */
send.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Settings\EmailVerificationController::send
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:33
 * @route '/settings/profile/verify-email/send'
 */
    const sendForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: send.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\EmailVerificationController::send
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:33
 * @route '/settings/profile/verify-email/send'
 */
        sendForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: send.url(options),
            method: 'post',
        })
    
    send.form = sendForm
/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::verify
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:65
 * @route '/settings/profile/verify-email'
 */
export const verify = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(options),
    method: 'post',
})

verify.definition = {
    methods: ["post"],
    url: '/settings/profile/verify-email',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::verify
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:65
 * @route '/settings/profile/verify-email'
 */
verify.url = (options?: RouteQueryOptions) => {
    return verify.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\EmailVerificationController::verify
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:65
 * @route '/settings/profile/verify-email'
 */
verify.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Settings\EmailVerificationController::verify
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:65
 * @route '/settings/profile/verify-email'
 */
    const verifyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: verify.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Settings\EmailVerificationController::verify
 * @see app/Http/Controllers/Settings/EmailVerificationController.php:65
 * @route '/settings/profile/verify-email'
 */
        verifyForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: verify.url(options),
            method: 'post',
        })
    
    verify.form = verifyForm
const emailVerification = {
    show: Object.assign(show, show),
send: Object.assign(send, send),
verify: Object.assign(verify, verify),
}

export default emailVerification