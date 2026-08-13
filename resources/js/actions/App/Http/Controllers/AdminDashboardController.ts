import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminDashboardController::__invoke
 * @see app/Http/Controllers/AdminDashboardController.php:17
 * @route '/admin/dashboard'
 */
const AdminDashboardController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: AdminDashboardController.url(options),
    method: 'get',
})

AdminDashboardController.definition = {
    methods: ["get","head"],
    url: '/admin/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminDashboardController::__invoke
 * @see app/Http/Controllers/AdminDashboardController.php:17
 * @route '/admin/dashboard'
 */
AdminDashboardController.url = (options?: RouteQueryOptions) => {
    return AdminDashboardController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminDashboardController::__invoke
 * @see app/Http/Controllers/AdminDashboardController.php:17
 * @route '/admin/dashboard'
 */
AdminDashboardController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: AdminDashboardController.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AdminDashboardController::__invoke
 * @see app/Http/Controllers/AdminDashboardController.php:17
 * @route '/admin/dashboard'
 */
AdminDashboardController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: AdminDashboardController.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AdminDashboardController::__invoke
 * @see app/Http/Controllers/AdminDashboardController.php:17
 * @route '/admin/dashboard'
 */
    const AdminDashboardControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: AdminDashboardController.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AdminDashboardController::__invoke
 * @see app/Http/Controllers/AdminDashboardController.php:17
 * @route '/admin/dashboard'
 */
        AdminDashboardControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: AdminDashboardController.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AdminDashboardController::__invoke
 * @see app/Http/Controllers/AdminDashboardController.php:17
 * @route '/admin/dashboard'
 */
        AdminDashboardControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: AdminDashboardController.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    AdminDashboardController.form = AdminDashboardControllerForm
export default AdminDashboardController