import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { getStudyProgramDefinition } from '@/lib/academic-programs';
import {
    InputField,
    SelectField,
    TextareaField,
} from '../components/form-controls';
import type { Assessment, Career } from '../types';

type Props = {
    assessment?: Assessment;
    careers: Career[];
};

export function AssessmentForm({ assessment, careers }: Props) {
    const action = assessment
        ? `/admin/assessments/${assessment.id}`
        : '/admin/assessments';

    const isAcademic = Boolean(
        assessment?.study_program &&
        assessment.study_program === assessment.career?.name &&
        getStudyProgramDefinition(assessment.study_program),
    );

    return (
        <Form
            action={action}
            method={assessment ? 'put' : 'post'}
            className="grid min-w-0 gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid min-w-0 gap-4 md:grid-cols-2">
                        <div className="min-w-0">
                            <SelectField
                                label="Jurusan"
                                name={isAcademic ? undefined : 'career_id'}
                                defaultValue={assessment?.career_id ?? ''}
                                disabled={isAcademic}
                                required
                            >
                                <option value="">Pilih jurusan</option>

                                {careers.map((career) => (
                                    <option key={career.id} value={career.id}>
                                        {career.name}
                                    </option>
                                ))}
                            </SelectField>

                            {isAcademic && (
                                <input
                                    type="hidden"
                                    name="career_id"
                                    value={assessment?.career_id}
                                />
                            )}
                        </div>

                        <InputField
                            label="Durasi Assessment (menit)"
                            type="number"
                            name="duration_minutes"
                            min={5}
                            max={180}
                            defaultValue={assessment?.duration_minutes ?? 50}
                            required
                        />
                    </div>

                    <InputField
                        label="Judul Assessment"
                        name="title"
                        defaultValue={assessment?.title ?? ''}
                        maxLength={180}
                        required
                    />

                    <TextareaField
                        label="Petunjuk Assessment"
                        name="description"
                        rows={5}
                        maxLength={2000}
                        defaultValue={assessment?.description ?? ''}
                        required
                    />

                    {isAcademic ? (
                        <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                            <input type="hidden" name="is_active" value="1" />

                            <p className="text-sm font-black">Status: Aktif</p>
                        </div>
                    ) : (
                        <SelectField
                            label="Status"
                            name="is_active"
                            defaultValue={
                                assessment?.is_active === false ? '0' : '1'
                            }
                            required
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </SelectField>
                    )}

                    {Object.keys(errors).length > 0 && (
                        <div className="rounded-xl border-2 border-destructive/30 p-4">
                            {Object.entries(errors).map(([field, message]) => (
                                <p
                                    key={field}
                                    className="text-xs leading-6 font-bold text-destructive"
                                >
                                    {message}
                                </p>
                            ))}
                        </div>
                    )}

                    <div>
                        <Button
                            type="submit"
                            disabled={processing}
                            className="w-full sm:w-auto"
                        >
                            {assessment ? (
                                <Save className="size-4" />
                            ) : (
                                <Plus className="size-4" />
                            )}

                            {processing
                                ? 'Menyimpan...'
                                : assessment
                                  ? 'Simpan perubahan'
                                  : 'Tambah Assessment'}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
