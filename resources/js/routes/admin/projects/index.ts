import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
import skills from './skills'
/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/projects',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::store
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\AdminController::update
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
export const update = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/projects/{portfolioProject}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminController::update
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
update.url = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::update
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
update.put = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminController::update
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
    const updateForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::update
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
        updateForm.put = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
export const destroy = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/projects/{portfolioProject}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
destroy.url = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
destroy.delete = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroy
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
    const destroyForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
        destroyForm.delete = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const projects = {
    store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
skills: Object.assign(skills, skills),
}

export default projects