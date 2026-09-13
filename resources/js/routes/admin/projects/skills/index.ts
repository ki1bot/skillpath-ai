import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
export const store = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/projects/{portfolioProject}/skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
store.url = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { portfolioProject: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
            args = { portfolioProject: args.slug }
        }
    
    if (Array.isArray(args)) {
        args = {
                    portfolioProject: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        portfolioProject: typeof args.portfolioProject === 'object'
                ? args.portfolioProject.slug
                : args.portfolioProject,
                }

    return store.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
store.post = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
    const storeForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
        storeForm.post = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(args, options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
export const destroy = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/projects/{portfolioProject}/skills/{skill}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
destroy.url = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    portfolioProject: args[0],
                    skill: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        portfolioProject: typeof args.portfolioProject === 'object'
                ? args.portfolioProject.slug
                : args.portfolioProject,
                                skill: typeof args.skill === 'object'
                ? args.skill.slug
                : args.skill,
                }

    return destroy.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
destroy.delete = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
    const destroyForm = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
        destroyForm.delete = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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