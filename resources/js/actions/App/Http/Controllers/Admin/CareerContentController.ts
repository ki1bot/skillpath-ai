import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CareerContentController::update
* @see app/Http/Controllers/Admin/CareerContentController.php:15
* @route '/admin/careers/{career}'
*/
export const update = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/careers/{career}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\CareerContentController::update
* @see app/Http/Controllers/Admin/CareerContentController.php:15
* @route '/admin/careers/{career}'
*/
update.url = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { career: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { career: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            career: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        career: typeof args.career === 'object'
        ? args.career.slug
        : args.career,
    }

    return update.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CareerContentController::update
* @see app/Http/Controllers/Admin/CareerContentController.php:15
* @route '/admin/careers/{career}'
*/
update.put = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CareerContentController::update
* @see app/Http/Controllers/Admin/CareerContentController.php:15
* @route '/admin/careers/{career}'
*/
const updateForm = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CareerContentController::update
* @see app/Http/Controllers/Admin/CareerContentController.php:15
* @route '/admin/careers/{career}'
*/
updateForm.put = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Admin\CareerContentController::destroy
* @see app/Http/Controllers/Admin/CareerContentController.php:115
* @route '/admin/careers/{career}'
*/
export const destroy = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/careers/{career}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CareerContentController::destroy
* @see app/Http/Controllers/Admin/CareerContentController.php:115
* @route '/admin/careers/{career}'
*/
destroy.url = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { career: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { career: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            career: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        career: typeof args.career === 'object'
        ? args.career.slug
        : args.career,
    }

    return destroy.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CareerContentController::destroy
* @see app/Http/Controllers/Admin/CareerContentController.php:115
* @route '/admin/careers/{career}'
*/
destroy.delete = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Admin\CareerContentController::destroy
* @see app/Http/Controllers/Admin/CareerContentController.php:115
* @route '/admin/careers/{career}'
*/
const destroyForm = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CareerContentController::destroy
* @see app/Http/Controllers/Admin/CareerContentController.php:115
* @route '/admin/careers/{career}'
*/
destroyForm.delete = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const CareerContentController = { update, destroy }

export default CareerContentController