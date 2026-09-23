import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::index
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:23
* @route '/admin/submissions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/submissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::index
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:23
* @route '/admin/submissions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::index
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:23
* @route '/admin/submissions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::index
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:23
* @route '/admin/submissions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::index
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:23
* @route '/admin/submissions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::index
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:23
* @route '/admin/submissions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::index
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:23
* @route '/admin/submissions'
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
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::update
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:221
* @route '/admin/submissions/{evaluation}'
*/
export const update = (args: { evaluation: number | { id: number } } | [evaluation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/admin/submissions/{evaluation}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::update
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:221
* @route '/admin/submissions/{evaluation}'
*/
update.url = (args: { evaluation: number | { id: number } } | [evaluation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { evaluation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { evaluation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            evaluation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        evaluation: typeof args.evaluation === 'object'
        ? args.evaluation.id
        : args.evaluation,
    }

    return update.definition.url
            .replace('{evaluation}', parsedArgs.evaluation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::update
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:221
* @route '/admin/submissions/{evaluation}'
*/
update.patch = (args: { evaluation: number | { id: number } } | [evaluation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::update
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:221
* @route '/admin/submissions/{evaluation}'
*/
const updateForm = (args: { evaluation: number | { id: number } } | [evaluation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::update
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:221
* @route '/admin/submissions/{evaluation}'
*/
updateForm.patch = (args: { evaluation: number | { id: number } } | [evaluation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const submissions = {
    index: Object.assign(index, index),
    update: Object.assign(update, update),
}

export default submissions