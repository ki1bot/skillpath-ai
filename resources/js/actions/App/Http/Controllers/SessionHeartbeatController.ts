import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
const SessionHeartbeatController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SessionHeartbeatController.url(options),
    method: 'get',
})

SessionHeartbeatController.definition = {
    methods: ["get","head"],
    url: '/session/heartbeat',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
SessionHeartbeatController.url = (options?: RouteQueryOptions) => {
    return SessionHeartbeatController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
SessionHeartbeatController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SessionHeartbeatController.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
SessionHeartbeatController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: SessionHeartbeatController.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
    const SessionHeartbeatControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: SessionHeartbeatController.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
        SessionHeartbeatControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: SessionHeartbeatController.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
        SessionHeartbeatControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: SessionHeartbeatController.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    SessionHeartbeatController.form = SessionHeartbeatControllerForm
export default SessionHeartbeatController