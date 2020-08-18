const query = `
query ($site: [String!], $section: [String]) {
  entries(site: $site, section:$section, type:"not link", orderBy:"title") {
    id
    title
    sectionHandle
    shortDescription
    slug
    ... on news_news_Entry {
       featuredImage {
        url(handle:"featuredImage")
      }
    }
    ... on person_person_Entry {
      featuredImage {
        url(handle:"featuredImage")
      }
    }
    ... on film_film_Entry {
      featuredImage {
        url(handle:"featuredImage")
      }
    }
  }
}
`

export {query}
