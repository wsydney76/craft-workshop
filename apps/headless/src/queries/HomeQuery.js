import {bodyContentFragment} from './BodyContent';

const query = bodyContentFragment +
    `query($site:[String!]) {
          entry(section:"home", site:$site) {
                id
                title
                ... on home_home_Entry {
                    teaser
                    bodyContent {
                        ...bodyContent
                    }
                }
            }
            siteInfo: globalSet(handle:"SiteInfo", site:$site) {
                ... on siteInfo_GlobalSet {
                    defaultFeaturedImage {
                        url(handle:"featuredImage")
                    }
                }
            }
        }
`;

export {query};
