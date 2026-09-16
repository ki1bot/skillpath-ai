import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/privacy-policy'
*/
const Controller546d1d979582dcab4cda77f98be026ca = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller546d1d979582dcab4cda77f98be026ca.url(options),
    method: 'get',
})

Controller546d1d979582dcab4cda77f98be026ca.definition = {
    methods: ["get","head"],
    url: '/privacy-policy',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/privacy-policy'
*/
Controller546d1d979582dcab4cda77f98be026ca.url = (options?: RouteQueryOptions) => {
    return Controller546d1d979582dcab4cda77f98be026ca.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/privacy-policy'
*/
Controller546d1d979582dcab4cda77f98be026ca.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller546d1d979582dcab4cda77f98be026ca.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/privacy-policy'
*/
Controller546d1d979582dcab4cda77f98be026ca.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller546d1d979582dcab4cda77f98be026ca.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/privacy-policy'
*/
const Controller546d1d979582dcab4cda77f98be026caForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller546d1d979582dcab4cda77f98be026ca.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/privacy-policy'
*/
Controller546d1d979582dcab4cda77f98be026caForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller546d1d979582dcab4cda77f98be026ca.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/privacy-policy'
*/
Controller546d1d979582dcab4cda77f98be026caForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller546d1d979582dcab4cda77f98be026ca.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller546d1d979582dcab4cda77f98be026ca.form = Controller546d1d979582dcab4cda77f98be026caForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/terms'
*/
const Controller619dc3a99425f668ea9cab64e6648cb4 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller619dc3a99425f668ea9cab64e6648cb4.url(options),
    method: 'get',
})

Controller619dc3a99425f668ea9cab64e6648cb4.definition = {
    methods: ["get","head"],
    url: '/terms',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/terms'
*/
Controller619dc3a99425f668ea9cab64e6648cb4.url = (options?: RouteQueryOptions) => {
    return Controller619dc3a99425f668ea9cab64e6648cb4.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/terms'
*/
Controller619dc3a99425f668ea9cab64e6648cb4.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller619dc3a99425f668ea9cab64e6648cb4.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/terms'
*/
Controller619dc3a99425f668ea9cab64e6648cb4.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller619dc3a99425f668ea9cab64e6648cb4.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/terms'
*/
const Controller619dc3a99425f668ea9cab64e6648cb4Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller619dc3a99425f668ea9cab64e6648cb4.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/terms'
*/
Controller619dc3a99425f668ea9cab64e6648cb4Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller619dc3a99425f668ea9cab64e6648cb4.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/terms'
*/
Controller619dc3a99425f668ea9cab64e6648cb4Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller619dc3a99425f668ea9cab64e6648cb4.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller619dc3a99425f668ea9cab64e6648cb4.form = Controller619dc3a99425f668ea9cab64e6648cb4Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/data-deletion'
*/
const Controller537be260890ef1448f48eb3ff9d83054 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller537be260890ef1448f48eb3ff9d83054.url(options),
    method: 'get',
})

Controller537be260890ef1448f48eb3ff9d83054.definition = {
    methods: ["get","head"],
    url: '/data-deletion',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/data-deletion'
*/
Controller537be260890ef1448f48eb3ff9d83054.url = (options?: RouteQueryOptions) => {
    return Controller537be260890ef1448f48eb3ff9d83054.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/data-deletion'
*/
Controller537be260890ef1448f48eb3ff9d83054.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller537be260890ef1448f48eb3ff9d83054.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/data-deletion'
*/
Controller537be260890ef1448f48eb3ff9d83054.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller537be260890ef1448f48eb3ff9d83054.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/data-deletion'
*/
const Controller537be260890ef1448f48eb3ff9d83054Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller537be260890ef1448f48eb3ff9d83054.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/data-deletion'
*/
Controller537be260890ef1448f48eb3ff9d83054Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller537be260890ef1448f48eb3ff9d83054.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/data-deletion'
*/
Controller537be260890ef1448f48eb3ff9d83054Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller537be260890ef1448f48eb3ff9d83054.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller537be260890ef1448f48eb3ff9d83054.form = Controller537be260890ef1448f48eb3ff9d83054Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/settings/appearance'
*/
const Controllere19ee86e9cf603ce1a59a1ec5d21dec5 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllere19ee86e9cf603ce1a59a1ec5d21dec5.url(options),
    method: 'get',
})

Controllere19ee86e9cf603ce1a59a1ec5d21dec5.definition = {
    methods: ["get","head"],
    url: '/settings/appearance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/settings/appearance'
*/
Controllere19ee86e9cf603ce1a59a1ec5d21dec5.url = (options?: RouteQueryOptions) => {
    return Controllere19ee86e9cf603ce1a59a1ec5d21dec5.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/settings/appearance'
*/
Controllere19ee86e9cf603ce1a59a1ec5d21dec5.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllere19ee86e9cf603ce1a59a1ec5d21dec5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/settings/appearance'
*/
Controllere19ee86e9cf603ce1a59a1ec5d21dec5.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllere19ee86e9cf603ce1a59a1ec5d21dec5.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/settings/appearance'
*/
const Controllere19ee86e9cf603ce1a59a1ec5d21dec5Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere19ee86e9cf603ce1a59a1ec5d21dec5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/settings/appearance'
*/
Controllere19ee86e9cf603ce1a59a1ec5d21dec5Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere19ee86e9cf603ce1a59a1ec5d21dec5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/settings/appearance'
*/
Controllere19ee86e9cf603ce1a59a1ec5d21dec5Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere19ee86e9cf603ce1a59a1ec5d21dec5.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllere19ee86e9cf603ce1a59a1ec5d21dec5.form = Controllere19ee86e9cf603ce1a59a1ec5d21dec5Form

/**
* Multiple routes resolve to \Inertia\Controller::Controller, so this export is a
* dictionary keyed by URI rather than a callable. Call a specific route with `Controller['<uri>'](...)`,
* or import the route by name from your generated `routes/` directory.
*/
const Controller = {
    '/privacy-policy': Controller546d1d979582dcab4cda77f98be026ca,
    '/terms': Controller619dc3a99425f668ea9cab64e6648cb4,
    '/data-deletion': Controller537be260890ef1448f48eb3ff9d83054,
    '/settings/appearance': Controllere19ee86e9cf603ce1a59a1ec5d21dec5,
}

export default Controller