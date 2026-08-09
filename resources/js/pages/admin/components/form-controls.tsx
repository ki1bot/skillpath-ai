import type {
    InputHTMLAttributes,
    ReactNode,
    SelectHTMLAttributes,
    TextareaHTMLAttributes,
} from 'react';
import { Input } from '@/components/ui/input';
import { fieldClass, selectClass, textareaClass } from '../styles';

type FieldProps = {
    label: string;
    children: ReactNode;
};

export function Field({ label, children }: FieldProps) {
    return (
        <label className={fieldClass}>
            <span>{label}</span>
            {children}
        </label>
    );
}

type InputFieldProps = InputHTMLAttributes<HTMLInputElement> & {
    label: string;
};

export function InputField({ label, ...props }: InputFieldProps) {
    return (
        <Field label={label}>
            <Input {...props} />
        </Field>
    );
}

type SelectFieldProps = SelectHTMLAttributes<HTMLSelectElement> & {
    label: string;
    children: ReactNode;
};

export function SelectField({
    label,
    children,
    className,
    ...props
}: SelectFieldProps) {
    return (
        <Field label={label}>
            <select
                {...props}
                className={[selectClass, className].filter(Boolean).join(' ')}
            >
                {children}
            </select>
        </Field>
    );
}

type TextareaFieldProps = TextareaHTMLAttributes<HTMLTextAreaElement> & {
    label: string;
};

export function TextareaField({
    label,
    className,
    ...props
}: TextareaFieldProps) {
    return (
        <Field label={label}>
            <textarea
                {...props}
                className={[textareaClass, className].filter(Boolean).join(' ')}
            />
        </Field>
    );
}

type ArrayFieldsProps = {
    name: string;
    label: string;
    values?: string[] | null;
    minimum?: number;
    required?: boolean;
};

export function ArrayFields({
    name,
    label,
    values,
    minimum = 3,
    required = true,
}: ArrayFieldsProps) {
    const length = Math.max(minimum, values?.length ?? 0);

    return (
        <div>
            <p className="mb-3 text-sm font-black">{label}</p>

            <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                {Array.from({ length }, (_, index) => (
                    <Input
                        key={index}
                        name={`${name}[]`}
                        defaultValue={values?.[index] ?? ''}
                        required={required}
                    />
                ))}
            </div>
        </div>
    );
}
