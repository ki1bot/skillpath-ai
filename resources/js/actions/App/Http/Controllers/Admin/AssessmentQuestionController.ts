import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::store
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:12
 * @route '/admin/questions'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/questions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::store
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:12
 * @route '/admin/questions'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::store
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:12
 * @route '/admin/questions'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::store
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:12
 * @route '/admin/questions'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::store
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:12
 * @route '/admin/questions'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::update
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:27
 * @route '/admin/questions/{question}'
 */
export const update = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/questions/{question}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::update
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:27
 * @route '/admin/questions/{question}'
 */
update.url = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { question: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { question: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    question: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        question: typeof args.question === 'object'
                ? args.question.id
                : args.question,
                }

    return update.definition.url
            .replace('{question}', parsedArgs.question.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::update
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:27
 * @route '/admin/questions/{question}'
 */
update.put = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::update
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:27
 * @route '/admin/questions/{question}'
 */
    const updateForm = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::update
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:27
 * @route '/admin/questions/{question}'
 */
        updateForm.put = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::destroy
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:43
 * @route '/admin/questions/{question}'
 */
export const destroy = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/questions/{question}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::destroy
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:43
 * @route '/admin/questions/{question}'
 */
destroy.url = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { question: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { question: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    question: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        question: typeof args.question === 'object'
                ? args.question.id
                : args.question,
                }

    return destroy.definition.url
            .replace('{question}', parsedArgs.question.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::destroy
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:43
 * @route '/admin/questions/{question}'
 */
destroy.delete = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::destroy
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:43
 * @route '/admin/questions/{question}'
 */
    const destroyForm = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\Admin\AssessmentQuestionController::destroy
 * @see app/Http/Controllers/Admin/AssessmentQuestionController.php:43
 * @route '/admin/questions/{question}'
 */
        destroyForm.delete = (args: { question: number | { id: number } } | [question: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const AssessmentQuestionController = { store, update, destroy }

export default AssessmentQuestionController