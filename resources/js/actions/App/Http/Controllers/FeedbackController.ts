import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\FeedbackController::index
 * @see app/Http/Controllers/FeedbackController.php:13
 * @route '/feedback'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/feedback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\FeedbackController::index
 * @see app/Http/Controllers/FeedbackController.php:13
 * @route '/feedback'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\FeedbackController::index
 * @see app/Http/Controllers/FeedbackController.php:13
 * @route '/feedback'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\FeedbackController::index
 * @see app/Http/Controllers/FeedbackController.php:13
 * @route '/feedback'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\FeedbackController::index
 * @see app/Http/Controllers/FeedbackController.php:13
 * @route '/feedback'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\FeedbackController::index
 * @see app/Http/Controllers/FeedbackController.php:13
 * @route '/feedback'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\FeedbackController::index
 * @see app/Http/Controllers/FeedbackController.php:13
 * @route '/feedback'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\FeedbackController::store
 * @see app/Http/Controllers/FeedbackController.php:32
 * @route '/feedback'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/feedback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\FeedbackController::store
 * @see app/Http/Controllers/FeedbackController.php:32
 * @route '/feedback'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\FeedbackController::store
 * @see app/Http/Controllers/FeedbackController.php:32
 * @route '/feedback'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\FeedbackController::store
 * @see app/Http/Controllers/FeedbackController.php:32
 * @route '/feedback'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\FeedbackController::store
 * @see app/Http/Controllers/FeedbackController.php:32
 * @route '/feedback'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
const FeedbackController = { index, store }

export default FeedbackController