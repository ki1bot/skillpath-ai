import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicPageController::home
 * @see app/Http/Controllers/PublicPageController.php:14
 * @route '/'
 */
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicPageController::home
 * @see app/Http/Controllers/PublicPageController.php:14
 * @route '/'
 */
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicPageController::home
 * @see app/Http/Controllers/PublicPageController.php:14
 * @route '/'
 */
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PublicPageController::home
 * @see app/Http/Controllers/PublicPageController.php:14
 * @route '/'
 */
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PublicPageController::home
 * @see app/Http/Controllers/PublicPageController.php:14
 * @route '/'
 */
    const homeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: home.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PublicPageController::home
 * @see app/Http/Controllers/PublicPageController.php:14
 * @route '/'
 */
        homeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: home.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PublicPageController::home
 * @see app/Http/Controllers/PublicPageController.php:14
 * @route '/'
 */
        homeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: home.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    home.form = homeForm
/**
* @see \App\Http\Controllers\PublicPageController::about
 * @see app/Http/Controllers/PublicPageController.php:142
 * @route '/tentang'
 */
export const about = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: about.url(options),
    method: 'get',
})

about.definition = {
    methods: ["get","head"],
    url: '/tentang',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicPageController::about
 * @see app/Http/Controllers/PublicPageController.php:142
 * @route '/tentang'
 */
about.url = (options?: RouteQueryOptions) => {
    return about.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicPageController::about
 * @see app/Http/Controllers/PublicPageController.php:142
 * @route '/tentang'
 */
about.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: about.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PublicPageController::about
 * @see app/Http/Controllers/PublicPageController.php:142
 * @route '/tentang'
 */
about.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: about.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PublicPageController::about
 * @see app/Http/Controllers/PublicPageController.php:142
 * @route '/tentang'
 */
    const aboutForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: about.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PublicPageController::about
 * @see app/Http/Controllers/PublicPageController.php:142
 * @route '/tentang'
 */
        aboutForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: about.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PublicPageController::about
 * @see app/Http/Controllers/PublicPageController.php:142
 * @route '/tentang'
 */
        aboutForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: about.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    about.form = aboutForm
/**
* @see \App\Http\Controllers\PublicPageController::careers
 * @see app/Http/Controllers/PublicPageController.php:63
 * @route '/karier'
 */
export const careers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: careers.url(options),
    method: 'get',
})

careers.definition = {
    methods: ["get","head"],
    url: '/karier',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicPageController::careers
 * @see app/Http/Controllers/PublicPageController.php:63
 * @route '/karier'
 */
careers.url = (options?: RouteQueryOptions) => {
    return careers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicPageController::careers
 * @see app/Http/Controllers/PublicPageController.php:63
 * @route '/karier'
 */
careers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: careers.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PublicPageController::careers
 * @see app/Http/Controllers/PublicPageController.php:63
 * @route '/karier'
 */
careers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: careers.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PublicPageController::careers
 * @see app/Http/Controllers/PublicPageController.php:63
 * @route '/karier'
 */
    const careersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: careers.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PublicPageController::careers
 * @see app/Http/Controllers/PublicPageController.php:63
 * @route '/karier'
 */
        careersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: careers.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PublicPageController::careers
 * @see app/Http/Controllers/PublicPageController.php:63
 * @route '/karier'
 */
        careersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: careers.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    careers.form = careersForm
/**
* @see \App\Http\Controllers\PublicPageController::career
 * @see app/Http/Controllers/PublicPageController.php:105
 * @route '/karier/{career}'
 */
export const career = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: career.url(args, options),
    method: 'get',
})

career.definition = {
    methods: ["get","head"],
    url: '/karier/{career}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicPageController::career
 * @see app/Http/Controllers/PublicPageController.php:105
 * @route '/karier/{career}'
 */
career.url = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return career.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicPageController::career
 * @see app/Http/Controllers/PublicPageController.php:105
 * @route '/karier/{career}'
 */
career.get = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: career.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PublicPageController::career
 * @see app/Http/Controllers/PublicPageController.php:105
 * @route '/karier/{career}'
 */
career.head = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: career.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\PublicPageController::career
 * @see app/Http/Controllers/PublicPageController.php:105
 * @route '/karier/{career}'
 */
    const careerForm = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: career.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\PublicPageController::career
 * @see app/Http/Controllers/PublicPageController.php:105
 * @route '/karier/{career}'
 */
        careerForm.get = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: career.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\PublicPageController::career
 * @see app/Http/Controllers/PublicPageController.php:105
 * @route '/karier/{career}'
 */
        careerForm.head = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: career.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    career.form = careerForm
const PublicPageController = { home, about, careers, career }

export default PublicPageController