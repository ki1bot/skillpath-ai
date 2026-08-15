import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\RoadmapController::index
 * @see app/Http/Controllers/RoadmapController.php:23
 * @route '/roadmap'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/roadmap',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoadmapController::index
 * @see app/Http/Controllers/RoadmapController.php:23
 * @route '/roadmap'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoadmapController::index
 * @see app/Http/Controllers/RoadmapController.php:23
 * @route '/roadmap'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RoadmapController::index
 * @see app/Http/Controllers/RoadmapController.php:23
 * @route '/roadmap'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\RoadmapController::index
 * @see app/Http/Controllers/RoadmapController.php:23
 * @route '/roadmap'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\RoadmapController::index
 * @see app/Http/Controllers/RoadmapController.php:23
 * @route '/roadmap'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\RoadmapController::index
 * @see app/Http/Controllers/RoadmapController.php:23
 * @route '/roadmap'
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
* @see \App\Http\Controllers\RoadmapController::material
 * @see app/Http/Controllers/RoadmapController.php:139
 * @route '/roadmap/materials/{material}'
 */
export const material = (args: { material: string | { slug: string } } | [material: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: material.url(args, options),
    method: 'get',
})

material.definition = {
    methods: ["get","head"],
    url: '/roadmap/materials/{material}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoadmapController::material
 * @see app/Http/Controllers/RoadmapController.php:139
 * @route '/roadmap/materials/{material}'
 */
material.url = (args: { material: string | { slug: string } } | [material: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { material: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
            args = { material: args.slug }
        }
    
    if (Array.isArray(args)) {
        args = {
                    material: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        material: typeof args.material === 'object'
                ? args.material.slug
                : args.material,
                }

    return material.definition.url
            .replace('{material}', parsedArgs.material.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoadmapController::material
 * @see app/Http/Controllers/RoadmapController.php:139
 * @route '/roadmap/materials/{material}'
 */
material.get = (args: { material: string | { slug: string } } | [material: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: material.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\RoadmapController::material
 * @see app/Http/Controllers/RoadmapController.php:139
 * @route '/roadmap/materials/{material}'
 */
material.head = (args: { material: string | { slug: string } } | [material: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: material.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\RoadmapController::material
 * @see app/Http/Controllers/RoadmapController.php:139
 * @route '/roadmap/materials/{material}'
 */
    const materialForm = (args: { material: string | { slug: string } } | [material: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: material.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\RoadmapController::material
 * @see app/Http/Controllers/RoadmapController.php:139
 * @route '/roadmap/materials/{material}'
 */
        materialForm.get = (args: { material: string | { slug: string } } | [material: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: material.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\RoadmapController::material
 * @see app/Http/Controllers/RoadmapController.php:139
 * @route '/roadmap/materials/{material}'
 */
        materialForm.head = (args: { material: string | { slug: string } } | [material: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: material.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    material.form = materialForm
/**
* @see \App\Http\Controllers\RoadmapController::logProgress
 * @see app/Http/Controllers/RoadmapController.php:312
 * @route '/roadmap/items/{roadmapItem}/progress'
 */
export const logProgress = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: logProgress.url(args, options),
    method: 'patch',
})

logProgress.definition = {
    methods: ["patch"],
    url: '/roadmap/items/{roadmapItem}/progress',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\RoadmapController::logProgress
 * @see app/Http/Controllers/RoadmapController.php:312
 * @route '/roadmap/items/{roadmapItem}/progress'
 */
logProgress.url = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { roadmapItem: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { roadmapItem: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    roadmapItem: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        roadmapItem: typeof args.roadmapItem === 'object'
                ? args.roadmapItem.id
                : args.roadmapItem,
                }

    return logProgress.definition.url
            .replace('{roadmapItem}', parsedArgs.roadmapItem.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoadmapController::logProgress
 * @see app/Http/Controllers/RoadmapController.php:312
 * @route '/roadmap/items/{roadmapItem}/progress'
 */
logProgress.patch = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: logProgress.url(args, options),
    method: 'patch',
})

    /**
* @see \App\Http\Controllers\RoadmapController::logProgress
 * @see app/Http/Controllers/RoadmapController.php:312
 * @route '/roadmap/items/{roadmapItem}/progress'
 */
    const logProgressForm = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: logProgress.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PATCH',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\RoadmapController::logProgress
 * @see app/Http/Controllers/RoadmapController.php:312
 * @route '/roadmap/items/{roadmapItem}/progress'
 */
        logProgressForm.patch = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: logProgress.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PATCH',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    logProgress.form = logProgressForm
/**
* @see \App\Http\Controllers\RoadmapController::evaluate
 * @see app/Http/Controllers/RoadmapController.php:407
 * @route '/roadmap/items/{roadmapItem}/evaluate'
 */
export const evaluate = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: evaluate.url(args, options),
    method: 'post',
})

evaluate.definition = {
    methods: ["post"],
    url: '/roadmap/items/{roadmapItem}/evaluate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RoadmapController::evaluate
 * @see app/Http/Controllers/RoadmapController.php:407
 * @route '/roadmap/items/{roadmapItem}/evaluate'
 */
evaluate.url = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { roadmapItem: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { roadmapItem: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    roadmapItem: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        roadmapItem: typeof args.roadmapItem === 'object'
                ? args.roadmapItem.id
                : args.roadmapItem,
                }

    return evaluate.definition.url
            .replace('{roadmapItem}', parsedArgs.roadmapItem.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoadmapController::evaluate
 * @see app/Http/Controllers/RoadmapController.php:407
 * @route '/roadmap/items/{roadmapItem}/evaluate'
 */
evaluate.post = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: evaluate.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\RoadmapController::evaluate
 * @see app/Http/Controllers/RoadmapController.php:407
 * @route '/roadmap/items/{roadmapItem}/evaluate'
 */
    const evaluateForm = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: evaluate.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\RoadmapController::evaluate
 * @see app/Http/Controllers/RoadmapController.php:407
 * @route '/roadmap/items/{roadmapItem}/evaluate'
 */
        evaluateForm.post = (args: { roadmapItem: number | { id: number } } | [roadmapItem: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: evaluate.url(args, options),
            method: 'post',
        })
    
    evaluate.form = evaluateForm
const RoadmapController = { index, material, logProgress, evaluate }

export default RoadmapController