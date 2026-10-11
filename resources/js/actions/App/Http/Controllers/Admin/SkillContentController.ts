import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\SkillContentController::update
* @see app/Http/Controllers/Admin/SkillContentController.php:13
* @route '/admin/skills/{skill}'
*/
export const update = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/skills/{skill}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\SkillContentController::update
* @see app/Http/Controllers/Admin/SkillContentController.php:13
* @route '/admin/skills/{skill}'
*/
update.url = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { skill: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { skill: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            skill: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        skill: typeof args.skill === 'object'
        ? args.skill.slug
        : args.skill,
    }

    return update.definition.url
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\SkillContentController::update
* @see app/Http/Controllers/Admin/SkillContentController.php:13
* @route '/admin/skills/{skill}'
*/
update.put = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\SkillContentController::update
* @see app/Http/Controllers/Admin/SkillContentController.php:13
* @route '/admin/skills/{skill}'
*/
const updateForm = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\SkillContentController::update
* @see app/Http/Controllers/Admin/SkillContentController.php:13
* @route '/admin/skills/{skill}'
*/
updateForm.put = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const SkillContentController = { update }

export default SkillContentController