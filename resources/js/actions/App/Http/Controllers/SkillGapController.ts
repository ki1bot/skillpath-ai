import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SkillGapController::index
 * @see app/Http/Controllers/SkillGapController.php:14
 * @route '/skills'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/skills',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SkillGapController::index
 * @see app/Http/Controllers/SkillGapController.php:14
 * @route '/skills'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SkillGapController::index
 * @see app/Http/Controllers/SkillGapController.php:14
 * @route '/skills'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\SkillGapController::index
 * @see app/Http/Controllers/SkillGapController.php:14
 * @route '/skills'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\SkillGapController::index
 * @see app/Http/Controllers/SkillGapController.php:14
 * @route '/skills'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\SkillGapController::index
 * @see app/Http/Controllers/SkillGapController.php:14
 * @route '/skills'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\SkillGapController::index
 * @see app/Http/Controllers/SkillGapController.php:14
 * @route '/skills'
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
const SkillGapController = { index }

export default SkillGapController