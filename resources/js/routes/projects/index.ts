import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:18
 * @route '/projects'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/projects',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:18
 * @route '/projects'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:18
 * @route '/projects'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:18
 * @route '/projects'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:18
 * @route '/projects'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:18
 * @route '/projects'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:18
 * @route '/projects'
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
* @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:49
 * @route '/projects/{portfolioProject}'
 */
export const show = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/projects/{portfolioProject}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:49
 * @route '/projects/{portfolioProject}'
 */
show.url = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:49
 * @route '/projects/{portfolioProject}'
 */
show.get = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:49
 * @route '/projects/{portfolioProject}'
 */
show.head = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:49
 * @route '/projects/{portfolioProject}'
 */
    const showForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:49
 * @route '/projects/{portfolioProject}'
 */
        showForm.get = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:49
 * @route '/projects/{portfolioProject}'
 */
        showForm.head = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\ProjectController::start
 * @see app/Http/Controllers/ProjectController.php:92
 * @route '/projects/{portfolioProject}/start'
 */
export const start = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(args, options),
    method: 'post',
})

start.definition = {
    methods: ["post"],
    url: '/projects/{portfolioProject}/start',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ProjectController::start
 * @see app/Http/Controllers/ProjectController.php:92
 * @route '/projects/{portfolioProject}/start'
 */
start.url = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return start.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectController::start
 * @see app/Http/Controllers/ProjectController.php:92
 * @route '/projects/{portfolioProject}/start'
 */
start.post = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ProjectController::start
 * @see app/Http/Controllers/ProjectController.php:92
 * @route '/projects/{portfolioProject}/start'
 */
    const startForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: start.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProjectController::start
 * @see app/Http/Controllers/ProjectController.php:92
 * @route '/projects/{portfolioProject}/start'
 */
        startForm.post = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: start.url(args, options),
            method: 'post',
        })
    
    start.form = startForm
/**
* @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:116
 * @route '/projects/{portfolioProject}'
 */
export const update = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/projects/{portfolioProject}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:116
 * @route '/projects/{portfolioProject}'
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
* @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:116
 * @route '/projects/{portfolioProject}'
 */
update.patch = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:116
 * @route '/projects/{portfolioProject}'
 */
    const updateForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:116
 * @route '/projects/{portfolioProject}'
 */
        updateForm.patch = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
const projects = {
    index: Object.assign(index, index),
show: Object.assign(show, show),
start: Object.assign(start, start),
update: Object.assign(update, update),
}

export default projects