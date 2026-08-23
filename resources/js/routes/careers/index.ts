import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import publicMethodC5d39d from './public'
/**
* @see \App\Http\Controllers\PublicPageController::publicMethod
 * @see app/Http/Controllers/PublicPageController.php:62
 * @route '/karier'
 */
export const publicMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: publicMethod.url(options),
    method: 'get',
})

publicMethod.definition = {
    methods: ["get","head"],
    url: '/karier',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicPageController::publicMethod
 * @see app/Http/Controllers/PublicPageController.php:62
 * @route '/karier'
 */
publicMethod.url = (options?: RouteQueryOptions) => {
    return publicMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicPageController::publicMethod
 * @see app/Http/Controllers/PublicPageController.php:62
 * @route '/karier'
 */
publicMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: publicMethod.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PublicPageController::publicMethod
 * @see app/Http/Controllers/PublicPageController.php:62
 * @route '/karier'
 */
publicMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: publicMethod.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PublicPageController::publicMethod
 * @see app/Http/Controllers/PublicPageController.php:62
 * @route '/karier'
 */
    const publicMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: publicMethod.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PublicPageController::publicMethod
 * @see app/Http/Controllers/PublicPageController.php:62
 * @route '/karier'
 */
        publicMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: publicMethod.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PublicPageController::publicMethod
 * @see app/Http/Controllers/PublicPageController.php:62
 * @route '/karier'
 */
        publicMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: publicMethod.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    publicMethod.form = publicMethodForm
const careers = {
    public: Object.assign(publicMethod, publicMethodC5d39d),
}

export default careers