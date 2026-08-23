import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AssessmentController::show
 * @see app/Http/Controllers/AssessmentController.php:32
 * @route '/assessment'
 */
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/assessment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AssessmentController::show
 * @see app/Http/Controllers/AssessmentController.php:32
 * @route '/assessment'
 */
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::show
 * @see app/Http/Controllers/AssessmentController.php:32
 * @route '/assessment'
 */
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AssessmentController::show
 * @see app/Http/Controllers/AssessmentController.php:32
 * @route '/assessment'
 */
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AssessmentController::show
 * @see app/Http/Controllers/AssessmentController.php:32
 * @route '/assessment'
 */
    const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AssessmentController::show
 * @see app/Http/Controllers/AssessmentController.php:32
 * @route '/assessment'
 */
        showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AssessmentController::show
 * @see app/Http/Controllers/AssessmentController.php:32
 * @route '/assessment'
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
* @see \App\Http\Controllers\AssessmentController::submit
 * @see app/Http/Controllers/AssessmentController.php:138
 * @route '/assessment'
 */
export const submit = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/assessment',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AssessmentController::submit
 * @see app/Http/Controllers/AssessmentController.php:138
 * @route '/assessment'
 */
submit.url = (options?: RouteQueryOptions) => {
    return submit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssessmentController::submit
 * @see app/Http/Controllers/AssessmentController.php:138
 * @route '/assessment'
 */
submit.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AssessmentController::submit
 * @see app/Http/Controllers/AssessmentController.php:138
 * @route '/assessment'
 */
    const submitForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: submit.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AssessmentController::submit
 * @see app/Http/Controllers/AssessmentController.php:138
 * @route '/assessment'
 */
        submitForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: submit.url(options),
            method: 'post',
        })
    
    submit.form = submitForm
const AssessmentController = { show, submit }

export default AssessmentController