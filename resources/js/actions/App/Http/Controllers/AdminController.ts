import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::index
 * @see app/Http/Controllers/AdminController.php:21
 * @route '/admin'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::index
 * @see app/Http/Controllers/AdminController.php:21
 * @route '/admin'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::index
 * @see app/Http/Controllers/AdminController.php:21
 * @route '/admin'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\AdminController::index
 * @see app/Http/Controllers/AdminController.php:21
 * @route '/admin'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\AdminController::index
 * @see app/Http/Controllers/AdminController.php:21
 * @route '/admin'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\AdminController::index
 * @see app/Http/Controllers/AdminController.php:21
 * @route '/admin'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\AdminController::index
 * @see app/Http/Controllers/AdminController.php:21
 * @route '/admin'
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
* @see \App\Http\Controllers\AdminController::storeCareer
 * @see app/Http/Controllers/AdminController.php:78
 * @route '/admin/careers'
 */
export const storeCareer = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCareer.url(options),
    method: 'post',
})

storeCareer.definition = {
    methods: ["post"],
    url: '/admin/careers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::storeCareer
 * @see app/Http/Controllers/AdminController.php:78
 * @route '/admin/careers'
 */
storeCareer.url = (options?: RouteQueryOptions) => {
    return storeCareer.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::storeCareer
 * @see app/Http/Controllers/AdminController.php:78
 * @route '/admin/careers'
 */
storeCareer.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCareer.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::storeCareer
 * @see app/Http/Controllers/AdminController.php:78
 * @route '/admin/careers'
 */
    const storeCareerForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeCareer.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::storeCareer
 * @see app/Http/Controllers/AdminController.php:78
 * @route '/admin/careers'
 */
        storeCareerForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeCareer.url(options),
            method: 'post',
        })
    
    storeCareer.form = storeCareerForm
/**
* @see \App\Http\Controllers\AdminController::updateCareer
 * @see app/Http/Controllers/AdminController.php:102
 * @route '/admin/careers/{career}'
 */
export const updateCareer = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCareer.url(args, options),
    method: 'put',
})

updateCareer.definition = {
    methods: ["put"],
    url: '/admin/careers/{career}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminController::updateCareer
 * @see app/Http/Controllers/AdminController.php:102
 * @route '/admin/careers/{career}'
 */
updateCareer.url = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return updateCareer.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::updateCareer
 * @see app/Http/Controllers/AdminController.php:102
 * @route '/admin/careers/{career}'
 */
updateCareer.put = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateCareer.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminController::updateCareer
 * @see app/Http/Controllers/AdminController.php:102
 * @route '/admin/careers/{career}'
 */
    const updateCareerForm = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateCareer.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::updateCareer
 * @see app/Http/Controllers/AdminController.php:102
 * @route '/admin/careers/{career}'
 */
        updateCareerForm.put = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateCareer.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateCareer.form = updateCareerForm
/**
* @see \App\Http\Controllers\AdminController::destroyCareer
 * @see app/Http/Controllers/AdminController.php:129
 * @route '/admin/careers/{career}'
 */
export const destroyCareer = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyCareer.url(args, options),
    method: 'delete',
})

destroyCareer.definition = {
    methods: ["delete"],
    url: '/admin/careers/{career}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroyCareer
 * @see app/Http/Controllers/AdminController.php:129
 * @route '/admin/careers/{career}'
 */
destroyCareer.url = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return destroyCareer.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroyCareer
 * @see app/Http/Controllers/AdminController.php:129
 * @route '/admin/careers/{career}'
 */
destroyCareer.delete = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyCareer.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroyCareer
 * @see app/Http/Controllers/AdminController.php:129
 * @route '/admin/careers/{career}'
 */
    const destroyCareerForm = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroyCareer.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::destroyCareer
 * @see app/Http/Controllers/AdminController.php:129
 * @route '/admin/careers/{career}'
 */
        destroyCareerForm.delete = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroyCareer.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroyCareer.form = destroyCareerForm
/**
* @see \App\Http\Controllers\AdminController::attachCareerSkill
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
export const attachCareerSkill = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: attachCareerSkill.url(args, options),
    method: 'post',
})

attachCareerSkill.definition = {
    methods: ["post"],
    url: '/admin/careers/{career}/skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::attachCareerSkill
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
attachCareerSkill.url = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return attachCareerSkill.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::attachCareerSkill
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
attachCareerSkill.post = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: attachCareerSkill.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::attachCareerSkill
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
    const attachCareerSkillForm = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: attachCareerSkill.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::attachCareerSkill
 * @see app/Http/Controllers/AdminController.php:136
 * @route '/admin/careers/{career}/skills'
 */
        attachCareerSkillForm.post = (args: { career: string | { slug: string } } | [career: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: attachCareerSkill.url(args, options),
            method: 'post',
        })
    
    attachCareerSkill.form = attachCareerSkillForm
/**
* @see \App\Http\Controllers\AdminController::removeCareerSkill
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
export const removeCareerSkill = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeCareerSkill.url(args, options),
    method: 'delete',
})

removeCareerSkill.definition = {
    methods: ["delete"],
    url: '/admin/careers/{career}/skills/{skill}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::removeCareerSkill
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
removeCareerSkill.url = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    career: args[0],
                    skill: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        career: typeof args.career === 'object'
                ? args.career.slug
                : args.career,
                                skill: typeof args.skill === 'object'
                ? args.skill.slug
                : args.skill,
                }

    return removeCareerSkill.definition.url
            .replace('{career}', parsedArgs.career.toString())
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::removeCareerSkill
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
removeCareerSkill.delete = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeCareerSkill.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::removeCareerSkill
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
    const removeCareerSkillForm = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: removeCareerSkill.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::removeCareerSkill
 * @see app/Http/Controllers/AdminController.php:158
 * @route '/admin/careers/{career}/skills/{skill}'
 */
        removeCareerSkillForm.delete = (args: { career: string | { slug: string }, skill: string | { slug: string } } | [career: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: removeCareerSkill.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    removeCareerSkill.form = removeCareerSkillForm
/**
* @see \App\Http\Controllers\AdminController::storeSkill
 * @see app/Http/Controllers/AdminController.php:167
 * @route '/admin/skills'
 */
export const storeSkill = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeSkill.url(options),
    method: 'post',
})

storeSkill.definition = {
    methods: ["post"],
    url: '/admin/skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::storeSkill
 * @see app/Http/Controllers/AdminController.php:167
 * @route '/admin/skills'
 */
storeSkill.url = (options?: RouteQueryOptions) => {
    return storeSkill.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::storeSkill
 * @see app/Http/Controllers/AdminController.php:167
 * @route '/admin/skills'
 */
storeSkill.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeSkill.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::storeSkill
 * @see app/Http/Controllers/AdminController.php:167
 * @route '/admin/skills'
 */
    const storeSkillForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeSkill.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::storeSkill
 * @see app/Http/Controllers/AdminController.php:167
 * @route '/admin/skills'
 */
        storeSkillForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeSkill.url(options),
            method: 'post',
        })
    
    storeSkill.form = storeSkillForm
/**
* @see \App\Http\Controllers\AdminController::updateSkill
 * @see app/Http/Controllers/AdminController.php:187
 * @route '/admin/skills/{skill}'
 */
export const updateSkill = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateSkill.url(args, options),
    method: 'put',
})

updateSkill.definition = {
    methods: ["put"],
    url: '/admin/skills/{skill}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminController::updateSkill
 * @see app/Http/Controllers/AdminController.php:187
 * @route '/admin/skills/{skill}'
 */
updateSkill.url = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return updateSkill.definition.url
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::updateSkill
 * @see app/Http/Controllers/AdminController.php:187
 * @route '/admin/skills/{skill}'
 */
updateSkill.put = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateSkill.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminController::updateSkill
 * @see app/Http/Controllers/AdminController.php:187
 * @route '/admin/skills/{skill}'
 */
    const updateSkillForm = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateSkill.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::updateSkill
 * @see app/Http/Controllers/AdminController.php:187
 * @route '/admin/skills/{skill}'
 */
        updateSkillForm.put = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateSkill.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateSkill.form = updateSkillForm
/**
* @see \App\Http\Controllers\AdminController::destroySkill
 * @see app/Http/Controllers/AdminController.php:210
 * @route '/admin/skills/{skill}'
 */
export const destroySkill = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroySkill.url(args, options),
    method: 'delete',
})

destroySkill.definition = {
    methods: ["delete"],
    url: '/admin/skills/{skill}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroySkill
 * @see app/Http/Controllers/AdminController.php:210
 * @route '/admin/skills/{skill}'
 */
destroySkill.url = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return destroySkill.definition.url
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroySkill
 * @see app/Http/Controllers/AdminController.php:210
 * @route '/admin/skills/{skill}'
 */
destroySkill.delete = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroySkill.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroySkill
 * @see app/Http/Controllers/AdminController.php:210
 * @route '/admin/skills/{skill}'
 */
    const destroySkillForm = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroySkill.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::destroySkill
 * @see app/Http/Controllers/AdminController.php:210
 * @route '/admin/skills/{skill}'
 */
        destroySkillForm.delete = (args: { skill: string | { slug: string } } | [skill: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroySkill.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroySkill.form = destroySkillForm
/**
* @see \App\Http\Controllers\AdminController::storePrerequisite
 * @see app/Http/Controllers/AdminController.php:217
 * @route '/admin/prerequisites'
 */
export const storePrerequisite = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storePrerequisite.url(options),
    method: 'post',
})

storePrerequisite.definition = {
    methods: ["post"],
    url: '/admin/prerequisites',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::storePrerequisite
 * @see app/Http/Controllers/AdminController.php:217
 * @route '/admin/prerequisites'
 */
storePrerequisite.url = (options?: RouteQueryOptions) => {
    return storePrerequisite.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::storePrerequisite
 * @see app/Http/Controllers/AdminController.php:217
 * @route '/admin/prerequisites'
 */
storePrerequisite.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storePrerequisite.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::storePrerequisite
 * @see app/Http/Controllers/AdminController.php:217
 * @route '/admin/prerequisites'
 */
    const storePrerequisiteForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storePrerequisite.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::storePrerequisite
 * @see app/Http/Controllers/AdminController.php:217
 * @route '/admin/prerequisites'
 */
        storePrerequisiteForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storePrerequisite.url(options),
            method: 'post',
        })
    
    storePrerequisite.form = storePrerequisiteForm
/**
* @see \App\Http\Controllers\AdminController::destroyPrerequisite
 * @see app/Http/Controllers/AdminController.php:254
 * @route '/admin/prerequisites/{id}'
 */
export const destroyPrerequisite = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyPrerequisite.url(args, options),
    method: 'delete',
})

destroyPrerequisite.definition = {
    methods: ["delete"],
    url: '/admin/prerequisites/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroyPrerequisite
 * @see app/Http/Controllers/AdminController.php:254
 * @route '/admin/prerequisites/{id}'
 */
destroyPrerequisite.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    id: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        id: args.id,
                }

    return destroyPrerequisite.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroyPrerequisite
 * @see app/Http/Controllers/AdminController.php:254
 * @route '/admin/prerequisites/{id}'
 */
destroyPrerequisite.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyPrerequisite.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroyPrerequisite
 * @see app/Http/Controllers/AdminController.php:254
 * @route '/admin/prerequisites/{id}'
 */
    const destroyPrerequisiteForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroyPrerequisite.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::destroyPrerequisite
 * @see app/Http/Controllers/AdminController.php:254
 * @route '/admin/prerequisites/{id}'
 */
        destroyPrerequisiteForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroyPrerequisite.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroyPrerequisite.form = destroyPrerequisiteForm
/**
* @see \App\Http\Controllers\AdminController::storeAssessment
 * @see app/Http/Controllers/AdminController.php:263
 * @route '/admin/assessments'
 */
export const storeAssessment = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeAssessment.url(options),
    method: 'post',
})

storeAssessment.definition = {
    methods: ["post"],
    url: '/admin/assessments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::storeAssessment
 * @see app/Http/Controllers/AdminController.php:263
 * @route '/admin/assessments'
 */
storeAssessment.url = (options?: RouteQueryOptions) => {
    return storeAssessment.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::storeAssessment
 * @see app/Http/Controllers/AdminController.php:263
 * @route '/admin/assessments'
 */
storeAssessment.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeAssessment.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::storeAssessment
 * @see app/Http/Controllers/AdminController.php:263
 * @route '/admin/assessments'
 */
    const storeAssessmentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeAssessment.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::storeAssessment
 * @see app/Http/Controllers/AdminController.php:263
 * @route '/admin/assessments'
 */
        storeAssessmentForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeAssessment.url(options),
            method: 'post',
        })
    
    storeAssessment.form = storeAssessmentForm
/**
* @see \App\Http\Controllers\AdminController::updateAssessment
 * @see app/Http/Controllers/AdminController.php:278
 * @route '/admin/assessments/{assessment}'
 */
export const updateAssessment = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateAssessment.url(args, options),
    method: 'put',
})

updateAssessment.definition = {
    methods: ["put"],
    url: '/admin/assessments/{assessment}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminController::updateAssessment
 * @see app/Http/Controllers/AdminController.php:278
 * @route '/admin/assessments/{assessment}'
 */
updateAssessment.url = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { assessment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { assessment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    assessment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        assessment: typeof args.assessment === 'object'
                ? args.assessment.id
                : args.assessment,
                }

    return updateAssessment.definition.url
            .replace('{assessment}', parsedArgs.assessment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::updateAssessment
 * @see app/Http/Controllers/AdminController.php:278
 * @route '/admin/assessments/{assessment}'
 */
updateAssessment.put = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateAssessment.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminController::updateAssessment
 * @see app/Http/Controllers/AdminController.php:278
 * @route '/admin/assessments/{assessment}'
 */
    const updateAssessmentForm = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateAssessment.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::updateAssessment
 * @see app/Http/Controllers/AdminController.php:278
 * @route '/admin/assessments/{assessment}'
 */
        updateAssessmentForm.put = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateAssessment.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateAssessment.form = updateAssessmentForm
/**
* @see \App\Http\Controllers\AdminController::destroyAssessment
 * @see app/Http/Controllers/AdminController.php:295
 * @route '/admin/assessments/{assessment}'
 */
export const destroyAssessment = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAssessment.url(args, options),
    method: 'delete',
})

destroyAssessment.definition = {
    methods: ["delete"],
    url: '/admin/assessments/{assessment}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroyAssessment
 * @see app/Http/Controllers/AdminController.php:295
 * @route '/admin/assessments/{assessment}'
 */
destroyAssessment.url = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { assessment: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { assessment: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    assessment: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        assessment: typeof args.assessment === 'object'
                ? args.assessment.id
                : args.assessment,
                }

    return destroyAssessment.definition.url
            .replace('{assessment}', parsedArgs.assessment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroyAssessment
 * @see app/Http/Controllers/AdminController.php:295
 * @route '/admin/assessments/{assessment}'
 */
destroyAssessment.delete = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAssessment.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroyAssessment
 * @see app/Http/Controllers/AdminController.php:295
 * @route '/admin/assessments/{assessment}'
 */
    const destroyAssessmentForm = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroyAssessment.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::destroyAssessment
 * @see app/Http/Controllers/AdminController.php:295
 * @route '/admin/assessments/{assessment}'
 */
        destroyAssessmentForm.delete = (args: { assessment: number | { id: number } } | [assessment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroyAssessment.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroyAssessment.form = destroyAssessmentForm
/**
* @see \App\Http\Controllers\AdminController::storeMaterial
 * @see app/Http/Controllers/AdminController.php:331
 * @route '/admin/materials'
 */
export const storeMaterial = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeMaterial.url(options),
    method: 'post',
})

storeMaterial.definition = {
    methods: ["post"],
    url: '/admin/materials',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::storeMaterial
 * @see app/Http/Controllers/AdminController.php:331
 * @route '/admin/materials'
 */
storeMaterial.url = (options?: RouteQueryOptions) => {
    return storeMaterial.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::storeMaterial
 * @see app/Http/Controllers/AdminController.php:331
 * @route '/admin/materials'
 */
storeMaterial.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeMaterial.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::storeMaterial
 * @see app/Http/Controllers/AdminController.php:331
 * @route '/admin/materials'
 */
    const storeMaterialForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeMaterial.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::storeMaterial
 * @see app/Http/Controllers/AdminController.php:331
 * @route '/admin/materials'
 */
        storeMaterialForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeMaterial.url(options),
            method: 'post',
        })
    
    storeMaterial.form = storeMaterialForm
/**
* @see \App\Http\Controllers\AdminController::updateMaterial
 * @see app/Http/Controllers/AdminController.php:346
 * @route '/admin/materials/{learningMaterial}'
 */
export const updateMaterial = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateMaterial.url(args, options),
    method: 'put',
})

updateMaterial.definition = {
    methods: ["put"],
    url: '/admin/materials/{learningMaterial}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminController::updateMaterial
 * @see app/Http/Controllers/AdminController.php:346
 * @route '/admin/materials/{learningMaterial}'
 */
updateMaterial.url = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { learningMaterial: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
            args = { learningMaterial: args.slug }
        }
    
    if (Array.isArray(args)) {
        args = {
                    learningMaterial: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        learningMaterial: typeof args.learningMaterial === 'object'
                ? args.learningMaterial.slug
                : args.learningMaterial,
                }

    return updateMaterial.definition.url
            .replace('{learningMaterial}', parsedArgs.learningMaterial.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::updateMaterial
 * @see app/Http/Controllers/AdminController.php:346
 * @route '/admin/materials/{learningMaterial}'
 */
updateMaterial.put = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateMaterial.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminController::updateMaterial
 * @see app/Http/Controllers/AdminController.php:346
 * @route '/admin/materials/{learningMaterial}'
 */
    const updateMaterialForm = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateMaterial.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::updateMaterial
 * @see app/Http/Controllers/AdminController.php:346
 * @route '/admin/materials/{learningMaterial}'
 */
        updateMaterialForm.put = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateMaterial.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateMaterial.form = updateMaterialForm
/**
* @see \App\Http\Controllers\AdminController::destroyMaterial
 * @see app/Http/Controllers/AdminController.php:364
 * @route '/admin/materials/{learningMaterial}'
 */
export const destroyMaterial = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyMaterial.url(args, options),
    method: 'delete',
})

destroyMaterial.definition = {
    methods: ["delete"],
    url: '/admin/materials/{learningMaterial}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroyMaterial
 * @see app/Http/Controllers/AdminController.php:364
 * @route '/admin/materials/{learningMaterial}'
 */
destroyMaterial.url = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { learningMaterial: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
            args = { learningMaterial: args.slug }
        }
    
    if (Array.isArray(args)) {
        args = {
                    learningMaterial: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        learningMaterial: typeof args.learningMaterial === 'object'
                ? args.learningMaterial.slug
                : args.learningMaterial,
                }

    return destroyMaterial.definition.url
            .replace('{learningMaterial}', parsedArgs.learningMaterial.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroyMaterial
 * @see app/Http/Controllers/AdminController.php:364
 * @route '/admin/materials/{learningMaterial}'
 */
destroyMaterial.delete = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyMaterial.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroyMaterial
 * @see app/Http/Controllers/AdminController.php:364
 * @route '/admin/materials/{learningMaterial}'
 */
    const destroyMaterialForm = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroyMaterial.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::destroyMaterial
 * @see app/Http/Controllers/AdminController.php:364
 * @route '/admin/materials/{learningMaterial}'
 */
        destroyMaterialForm.delete = (args: { learningMaterial: string | { slug: string } } | [learningMaterial: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroyMaterial.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroyMaterial.form = destroyMaterialForm
/**
* @see \App\Http\Controllers\AdminController::storeProject
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
export const storeProject = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeProject.url(options),
    method: 'post',
})

storeProject.definition = {
    methods: ["post"],
    url: '/admin/projects',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::storeProject
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
storeProject.url = (options?: RouteQueryOptions) => {
    return storeProject.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::storeProject
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
storeProject.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeProject.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::storeProject
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
    const storeProjectForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: storeProject.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::storeProject
 * @see app/Http/Controllers/AdminController.php:372
 * @route '/admin/projects'
 */
        storeProjectForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: storeProject.url(options),
            method: 'post',
        })
    
    storeProject.form = storeProjectForm
/**
* @see \App\Http\Controllers\AdminController::updateProject
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
export const updateProject = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProject.url(args, options),
    method: 'put',
})

updateProject.definition = {
    methods: ["put"],
    url: '/admin/projects/{portfolioProject}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\AdminController::updateProject
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
updateProject.url = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return updateProject.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::updateProject
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
updateProject.put = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateProject.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\AdminController::updateProject
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
    const updateProjectForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateProject.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::updateProject
 * @see app/Http/Controllers/AdminController.php:387
 * @route '/admin/projects/{portfolioProject}'
 */
        updateProjectForm.put = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateProject.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    updateProject.form = updateProjectForm
/**
* @see \App\Http\Controllers\AdminController::destroyProject
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
export const destroyProject = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyProject.url(args, options),
    method: 'delete',
})

destroyProject.definition = {
    methods: ["delete"],
    url: '/admin/projects/{portfolioProject}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::destroyProject
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
destroyProject.url = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return destroyProject.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::destroyProject
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
destroyProject.delete = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyProject.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::destroyProject
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
    const destroyProjectForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroyProject.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::destroyProject
 * @see app/Http/Controllers/AdminController.php:405
 * @route '/admin/projects/{portfolioProject}'
 */
        destroyProjectForm.delete = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroyProject.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroyProject.form = destroyProjectForm
/**
* @see \App\Http\Controllers\AdminController::attachProjectSkill
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
export const attachProjectSkill = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: attachProjectSkill.url(args, options),
    method: 'post',
})

attachProjectSkill.definition = {
    methods: ["post"],
    url: '/admin/projects/{portfolioProject}/skills',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::attachProjectSkill
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
attachProjectSkill.url = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return attachProjectSkill.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::attachProjectSkill
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
attachProjectSkill.post = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: attachProjectSkill.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\AdminController::attachProjectSkill
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
    const attachProjectSkillForm = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: attachProjectSkill.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::attachProjectSkill
 * @see app/Http/Controllers/AdminController.php:413
 * @route '/admin/projects/{portfolioProject}/skills'
 */
        attachProjectSkillForm.post = (args: { portfolioProject: string | { slug: string } } | [portfolioProject: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: attachProjectSkill.url(args, options),
            method: 'post',
        })
    
    attachProjectSkill.form = attachProjectSkillForm
/**
* @see \App\Http\Controllers\AdminController::removeProjectSkill
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
export const removeProjectSkill = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeProjectSkill.url(args, options),
    method: 'delete',
})

removeProjectSkill.definition = {
    methods: ["delete"],
    url: '/admin/projects/{portfolioProject}/skills/{skill}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::removeProjectSkill
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
removeProjectSkill.url = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions) => {
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

    return removeProjectSkill.definition.url
            .replace('{portfolioProject}', parsedArgs.portfolioProject.toString())
            .replace('{skill}', parsedArgs.skill.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::removeProjectSkill
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
removeProjectSkill.delete = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeProjectSkill.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\AdminController::removeProjectSkill
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
    const removeProjectSkillForm = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: removeProjectSkill.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\AdminController::removeProjectSkill
 * @see app/Http/Controllers/AdminController.php:433
 * @route '/admin/projects/{portfolioProject}/skills/{skill}'
 */
        removeProjectSkillForm.delete = (args: { portfolioProject: string | { slug: string }, skill: string | { slug: string } } | [portfolioProject: string | { slug: string }, skill: string | { slug: string } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: removeProjectSkill.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    removeProjectSkill.form = removeProjectSkillForm
const AdminController = { index, storeCareer, updateCareer, destroyCareer, attachCareerSkill, removeCareerSkill, storeSkill, updateSkill, destroySkill, storePrerequisite, destroyPrerequisite, storeAssessment, updateAssessment, destroyAssessment, storeMaterial, updateMaterial, destroyMaterial, storeProject, updateProject, destroyProject, attachProjectSkill, removeProjectSkill }

export default AdminController