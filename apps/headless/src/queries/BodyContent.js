export const bodyContentFragment = `
    fragment bodyContent on bodyContent_MatrixField {
  __typename
  ... on bodyContent_text_BlockType {
    text
  }
  ... on bodyContent_heading_BlockType {
    text
    tag
  }
  ... on bodyContent_image_BlockType {
    image {
      url @transform(handle: "contentImage")
      ... on images_Asset {
        copyright
      }
    }
    caption
  }
  ... on bodyContent_pullquote_BlockType {
    text
    textSource
  }
  ... on bodyContent_button_BlockType {
    target {
      id
      title
      sectionHandle
      siteId
      slug
    }
    caption
    color
  }
}
`;
