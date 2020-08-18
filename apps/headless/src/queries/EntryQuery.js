import {bodyContentFragment} from './BodyContent';

const query = bodyContentFragment +
    `query($site:[String!], $id: [QueryArgument]) {
          entry(id: $id, site:$site) {
                id
                title
                sectionHandle
                ... on news_news_Entry {
                  teaser
                  bodyContent {
                    ...bodyContent
                  }
                  featuredImage {
                    url(handle:"featuredImage")
                  }
                }
                ... on person_person_Entry {
                  teaser
                  bio                 
                  roles {
                      roleName
                      film {
                        id
                        title
                        slug       
                      }
                  }
                  bodyContent {
                    ...bodyContent
                  }
                  featuredImage {
                    url(handle:"featuredImage")
                  }
                }
                ... on film_film_Entry {
                  teaser
                  originalTitle
                  releaseYear
                  cast {
                    ... on cast_role_BlockType {
                      roleName
                      persons {
                        id
                        slug
                        title
                        sectionHandle
                      }
                    }
                  }
                  bodyContent {
                    ...bodyContent
                  }
                  featuredImage {
                    url(handle:"featuredImage")
                  }
                }
            }           
        }
`;

export {query};
