import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::update
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:194
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
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:194
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
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:194
* @route '/admin/submissions/{evaluation}'
*/
update.patch = (args: { evaluation: number | { id: number } } | [evaluation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\EvaluationSubmissionController::update
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:194
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
* @see app/Http/Controllers/Admin/EvaluationSubmissionController.php:194
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

const EvaluationSubmissionController = { update }

export default EvaluationSubmissionController