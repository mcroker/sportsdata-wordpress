/**
 * useBlockProps is a React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 *
 * RichText is a component that allows developers to render a contenteditable input,
 * providing users with the option to format block content to make it bold, italics,
 * linked, or use other formatting.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/richtext/
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Card, CardBody, CardHeader, TextControl } from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object}   param0
 * @param {Object}   param0.attributes
 * @param {string}   param0.attributes.message
 * @param {Function} param0.setAttributes
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes: { title, teamkey, maxrows, maxfuture }, setAttributes }) {
	return (
		<Card
			{...useBlockProps({ class: 'sd-now-and-next-editor' })}
		>
			<CardHeader>
				SportsData: Fixtures Now & Next
			</CardHeader>
			<CardBody>
				<TextControl
					label="Title"
					value={title}
					onChange={(value) =>
						setAttributes({ title: value })
					}
				/>
				<TextControl
					label="Team Identifier"
					value={teamkey}
					onChange={(value) =>
						setAttributes({ teamkey: value })
					}
				/>
				<TextControl
					label="Maximum Rows"
					value={maxrows}
					onChange={(value) =>
						setAttributes({ maxrows: parseInt(value) })
					}
				/>
				<TextControl
					label="Maximum Future Fixtures"
					value={maxfuture}
					onChange={(value) =>
						setAttributes({ maxfuture: parseInt(value) })
					}
				/>
			</CardBody>
		</Card>
	);
}
