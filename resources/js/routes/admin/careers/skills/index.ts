import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
export const store = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/careers/{career}/skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
store.url = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
store.post = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
    const storeForm = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
        storeForm.post = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
export const destroy = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/careers/{career}/skills/{skill}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
destroy.url = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    career: args[0],
                    skill: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        career: typeof args.career === 'object'
                ? args.career.slug
                : args.career,
                                skill: typeof args.skill === 'object'
                ? args.skill.slug
                : args.skill,
                }

    return destroy.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
destroy.delete = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
    const destroyForm = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
        destroyForm.delete = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const skills = {
    store: Object.assign(store, store),
destroy: Object.assign(destroy, destroy),
}

export default skills