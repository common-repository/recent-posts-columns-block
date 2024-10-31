(function(wp) {
    var __ = wp.i18n.__;
    var createElement = wp.element.createElement;
    var ServerSideRender = wp.serverSideRender;
    var InspectorControls = wp.editor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var ToggleControl = wp.components.ToggleControl;
    var RichText = wp.editor.RichText;
    var IconButton = wp.components.IconButton;
    
    wp.blocks.registerBlockType('tishonator/recent-posts-columns', {
        title: __('Recent Posts Columns', 'tishonator'),
        icon: 'excerpt-view',
        category: 'common',
        attributes: {
            posts: {
                type: 'array',
                default: [
                    { title: '', content: '', date: '', url: '' },
                    { title: '', content: '', date: '', url: '' },
                    { title: '', content: '', date: '', url: '' },
                ],
            },
            columnsCount: {
                type: 'number',
                default: 3,
            },
            excerptLength: {
                type: 'number',
                default: 50,
            },
            showPages: {
                type: 'boolean',
                default: true,
            },
            displayThumbnails: {
                type: 'boolean',
                default: true,
            },
            displayAuthor: {
                type: 'boolean',
                default: true,
            },
            displayDate: {
                type: 'boolean',
                default: true,
            },
            displayReadMore: {
                type: 'boolean',
                default: true,
            },
        },
        edit: function(props) {
            var attributes = props.attributes;
            var posts = attributes.posts;
            var setAttributes = props.setAttributes;

            function onUpdatePost(index, field, value) {
                var updatedPosts = posts.map(function(post, i) {
                    if (index === i) {
                        return { ...post, [field]: value };
                    }
                    return post;
                });
                setAttributes({ posts: updatedPosts });
            }

            function onAddPost() {
                var newPost = { title: '', content: '', date: '', url: '' };
                var updatedPosts = [ ...posts, newPost ];
                setAttributes({ posts: updatedPosts });
            }

            function onRemovePost(index) {
                var updatedPosts = [ ...posts ];
                updatedPosts.splice(index, 1);
                setAttributes({ posts: updatedPosts });
            }

           const controls = [
                createElement(InspectorControls, { key: 'inspector' },
                    createElement(PanelBody, { title: __('Posts', 'tishonator'), initialOpen: true },
                        createElement(TextControl, {
                            label: __('Number of Posts', 'tishonator'),
                            type: 'number',
                            value: attributes.posts.length,
                            onChange: function(value) {
                                var numPosts = parseInt(value);
                                var updatedPosts = [ ...posts ];
                                while (updatedPosts.length < numPosts) {
                                    updatedPosts.push({ title: '', content: '', date: '', url: '' });
                                }
                                updatedPosts = updatedPosts.slice(0, numPosts);
                                setAttributes({ posts: updatedPosts });
                            },
                        }),
                        createElement(TextControl, {
                            label: __('Columns Count', 'tishonator'),
                            type: 'number',
                            value: attributes.columnsCount,
                            onChange: function(value) {
                                setAttributes({ columnsCount: parseInt(value) });
                            },
                        }),
                        createElement(TextControl, {
                            label: __('Excerpt Length', 'tishonator'),
                            type: 'number',
                            value: attributes.excerptLength,
                            onChange: function(value) {
                                setAttributes({ excerptLength: parseInt(value) });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: __('Display Pages', 'tishonator'),
                            checked: attributes.showPages,
                            onChange: function(value) {
                                setAttributes({ showPages: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: __('Display Thumbnails', 'tishonator'),
                            checked: attributes.displayThumbnails,
                            onChange: function(value) {
                                setAttributes({ displayThumbnails: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: __('Display Author', 'tishonator'),
                            checked: attributes.displayAuthor,
                            onChange: function(value) {
                                setAttributes({ displayAuthor: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: __('Display Date', 'tishonator'),
                            checked: attributes.displayDate,
                            onChange: function(value) {
                                setAttributes({ displayDate: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: __('Display Read More', 'tishonator'),
                            checked: attributes.displayReadMore,
                            onChange: function(value) {
                                setAttributes({ displayReadMore: value });
                            },
                        })
                    )
                ),
            ];


            return [controls,
                      createElement('div', {}, 'Recent Posts Columns' ) ];
        },
        save: function() {

            return createElement('div', {}, 'Recent Posts Columns' );
        },
    });
})(window.wp);
