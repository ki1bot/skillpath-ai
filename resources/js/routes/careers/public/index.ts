import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicPageController::show
 * @see app/Http/Controllers/PublicPageController.php:104
 * @route '/karier/{career}'
 */
export const show = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/karier/{career}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicPageController::show
 * @see app/Http/Controllers/PublicPageController.php:104
 * @route '/karier/{career}'
 */
show.url = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicPageController::show
 * @see app/Http/Controllers/PublicPageController.php:104
 * @route '/karier/{career}'
 */
show.get = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PublicPageController::show
 * @see app/Http/Controllers/PublicPageController.php:104
 * @route '/karier/{career}'
 */
show.head = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PublicPageController::show
 * @see app/Http/Controllers/PublicPageController.php:104
 * @route '/karier/{career}'
 */
    const showForm = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PublicPageController::show
 * @see app/Http/Controllers/PublicPageController.php:104
 * @route '/karier/{career}'
 */
        showForm.get = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PublicPageController::show
 * @see app/Http/Controllers/PublicPageController.php:104
 * @route '/karier/{career}'
 */
        showForm.head = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
const publicMethod = {
    show: Object.assign(show, show),
}

export default publicMethod