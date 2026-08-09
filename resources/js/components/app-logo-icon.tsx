import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg
            {...props}
            viewBox="0 0 48 48"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <rect
                x="4"
                y="27"
                width="10"
                height="17"
                rx="2"
                fill="currentColor"
            />
            <rect
                x="19"
                y="18"
                width="10"
                height="26"
                rx="2"
                fill="currentColor"
            />
            <rect
                x="34"
                y="8"
                width="10"
                height="36"
                rx="2"
                fill="currentColor"
            />
            <path
                d="M8 18L20 8L28 13L40 3"
                stroke="currentColor"
                strokeWidth="4"
                strokeLinecap="round"
                strokeLinejoin="round"
            />
        </svg>
    );
}
