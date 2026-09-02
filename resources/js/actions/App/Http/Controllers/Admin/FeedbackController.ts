import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\FeedbackController::index
 * @see app/Http/Controllers/Admin/FeedbackController.php:15
 * @route '/admin/feedback'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/feedback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\FeedbackController::index
 * @see app/Http/Controllers/Admin/FeedbackController.php:15
 * @route '/admin/feedback'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\FeedbackController::index
 * @see app/Http/Controllers/Admin/FeedbackController.php:15
 * @route '/admin/feedback'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\FeedbackController::index
 * @see app/Http/Controllers/Admin/FeedbackController.php:15
 * @route '/admin/feedback'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\Admin\FeedbackController::index
 * @see app/Http/Controllers/Admin/FeedbackController.php:15
 * @route '/admin/feedback'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\Admin\FeedbackController::index
 * @see app/Http/Controllers/Admin/FeedbackController.php:15
 * @route '/admin/feedback'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\Admin\FeedbackController::index
 * @see app/Http/Controllers/Admin/FeedbackController.php:15
 * @route '/admin/feedback'
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
* @see \App\Http\Controllers\Admin\FeedbackController::update
 * @see app/Http/Controllers/Admin/FeedbackController.php:38
 * @route '/admin/feedback/{feedback}'
 */
export const update = (args: { feedback: number | { id: number } } | [feedback: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/admin/feedback/{feedback}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Admin\FeedbackController::update
 * @see app/Http/Controllers/Admin/FeedbackController.php:38
 * @route '/admin/feedback/{feedback}'
 */
update.url = (args: { feedback: number | { id: number } } | [feedback: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { feedback: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { feedback: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    feedback: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        feedback: typeof args.feedback === 'object'
                ? args.feedback.id
                : args.feedback,
                }

    return update.definition.url
            .replace('{feedback}', parsedArgs.feedback.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\FeedbackController::update
 * @see app/Http/Controllers/Admin/FeedbackController.php:38
 * @route '/admin/feedback/{feedback}'
 */
update.patch = (args: { feedback: number | { id: number } } | [feedback: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\Admin\FeedbackController::update
 * @see app/Http/Controllers/Admin/FeedbackController.php:38
 * @route '/admin/feedback/{feedback}'
 */
    const updateForm = (args: { feedback: number | { id: number } } | [feedback: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Admin\FeedbackController::update
 * @see app/Http/Controllers/Admin/FeedbackController.php:38
 * @route '/admin/feedback/{feedback}'
 */
        updateForm.patch = (args: { feedback: number | { id: number } } | [feedback: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
const FeedbackController = { index, update }

export default FeedbackController