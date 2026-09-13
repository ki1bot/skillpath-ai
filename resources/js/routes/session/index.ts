import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
export const heartbeat = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heartbeat.url(options),
    method: 'get',
})

heartbeat.definition = {
    methods: ["get","head"],
    url: '/session/heartbeat',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
heartbeat.url = (options?: RouteQueryOptions) => {
    return heartbeat.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
heartbeat.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heartbeat.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
heartbeat.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: heartbeat.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
    const heartbeatForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: heartbeat.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
        heartbeatForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: heartbeat.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SessionHeartbeatController::__invoke
 * @see app/Http/Controllers/SessionHeartbeatController.php:9
 * @route '/session/heartbeat'
 */
        heartbeatForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: heartbeat.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    heartbeat.form = heartbeatForm
const session = {
    heartbeat: Object.assign(heartbeat, heartbeat),
}

export default session